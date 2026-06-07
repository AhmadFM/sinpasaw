/**
 * denah-3d.js - Refactored 3D Navigation
 * Uses new API services for routing and data management
 */

import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { SVGLoader } from 'three/addons/loaders/SVGLoader.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

class DenahNavigation3D {
    constructor() {
        this.urlParams = new URLSearchParams(window.location.search);
        this.startNodeId = this.urlParams.get('start') || 'node-KB001';
        this.targetLapakId = this.urlParams.get('target') || 'KB001';
        
        // Get graph data from sessionStorage or fetch from API
        this.graphData = this.getGraphData();
        
        // Scene setup
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x1a1a1a);
        
        this.canvas = document.querySelector('#gameCanvas');
        this.camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 5000);
        this.renderer = new THREE.WebGLRenderer({ canvas: this.canvas, antialias: true });
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.renderer.shadowMap.enabled = true;
        
        // State
        this.walls = [];
        this.currentRouteMesh = null;
        this.route3D = [];
        
        // Loaders
        this.svgLoader = new SVGLoader();
        this.gltfLoader = new GLTFLoader();
        this.raycaster = new THREE.Raycaster();
        
        // Player
        this.player = new THREE.Group();
        this.PLAYER_RADIUS = 4;
        this.PLAYER_HEIGHT = 12;
        this.MOVE_SPEED = 1.25;
        
        // Input
        this.keys = {};
        this.mousePos = new THREE.Vector2();
    }

    /**
     * Get graph data from sessionStorage or fetch from API
     */
    getGraphData() {
        const cached = sessionStorage.getItem('graphData');
        if (cached) {
            return JSON.parse(cached);
        }
        // TODO: Fetch from API if not cached
        return { nodes: {}, edges: [] };
    }

    /**
     * Initialize the scene
     */
    async initialize() {
        this.setupLighting();
        this.setupFloor();
        this.setupPlayer();
        this.setupUI();
        this.setupEventListeners();
        
        // Load models asynchronously
        await this.loadPlayerModel();
        
        // Calculate and display route
        await this.calculateAndDisplayRoute();
        
        // Start animation loop
        this.animate();
        
        console.log('✓ 3D Navigation initialized');
    }

    /**
     * Setup lighting
     */
    setupLighting() {
        const ambientLight = new THREE.AmbientLight(0xffffff, 1.2);
        this.scene.add(ambientLight);
        
        const dirLight = new THREE.DirectionalLight(0xffffff, 1.5);
        dirLight.position.set(500, 1000, 500);
        dirLight.castShadow = true;
        dirLight.shadow.camera.top = 1000;
        dirLight.shadow.camera.bottom = -1000;
        dirLight.shadow.camera.left = -1000;
        dirLight.shadow.camera.right = 1000;
        dirLight.shadow.camera.near = 0.1;
        dirLight.shadow.camera.far = 2000;
        this.scene.add(dirLight);
    }

    /**
     * Setup floor
     */
    setupFloor() {
        const floorGeo = new THREE.PlaneGeometry(2000, 2000);
        const floorMat = new THREE.MeshStandardMaterial({ color: 0x3e3e42, roughness: 0.8 });
        const floor = new THREE.Mesh(floorGeo, floorMat);
        floor.rotation.x = -Math.PI / 2;
        floor.receiveShadow = true;
        this.scene.add(floor);
    }

    /**
     * Setup player
     */
    setupPlayer() {
        this.scene.add(this.player);
        
        // Set initial position
        if (this.graphData.nodes[this.startNodeId]) {
            const node = this.graphData.nodes[this.startNodeId];
            this.player.position.set(node.x, 0, node.y);
        } else {
            this.player.position.set(100, 0, 600);
        }
        
        // Camera follow player
        this.updateCameraPosition();
    }

    /**
     * Load player model
     */
    async loadPlayerModel() {
        return new Promise((resolve) => {
            this.gltfLoader.load('/models/creeper/creeper.gltf', (gltf) => {
                const model = gltf.scene;
                model.scale.set(6, 6, 6);
                model.traverse((object) => {
                    if (object.isMesh) {
                        object.castShadow = true;
                        object.receiveShadow = true;
                    }
                });
                this.player.add(model);
                resolve();
            }, undefined, (err) => {
                console.warn('Failed to load player model:', err);
                resolve();
            });
        });
    }

    /**
     * Setup UI elements
     */
    setupUI() {
        document.getElementById('targetName').textContent = this.targetLapakId;
        
        document.getElementById('btnResetCam').addEventListener('click', () => {
            this.updateCameraPosition();
        });
        
        document.getElementById('btnGyro').addEventListener('click', (e) => {
            e.target.classList.toggle('active');
            // TODO: Implement gyro sensor support
        });
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        window.addEventListener('keydown', (e) => this.keys[e.key.toLowerCase()] = true);
        window.addEventListener('keyup', (e) => this.keys[e.key.toLowerCase()] = false);
        window.addEventListener('mousemove', (e) => {
            this.mousePos.x = (e.clientX / window.innerWidth) * 2 - 1;
            this.mousePos.y = -(e.clientY / window.innerHeight) * 2 + 1;
        });
        window.addEventListener('resize', () => this.onWindowResize());
    }

    /**
     * Calculate and display route using API
     */
    async calculateAndDisplayRoute() {
        try {
            const response = await fetch('/api/denah/route', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    start_node_id: this.startNodeId,
                    end_node_id: `node-${this.targetLapakId}`,
                    include_coordinates: true,
                }),
            });
            
            const result = await response.json();
            if (result.success && result.data.path) {
                this.route3D = result.data.path;
                this.visualizeRoute();
            }
        } catch (error) {
            console.error('Error calculating route:', error);
        }
    }

    /**
     * Visualize route in 3D
     */
    visualizeRoute() {
        if (this.currentRouteMesh) {
            this.scene.remove(this.currentRouteMesh);
        }
        
        if (this.route3D.length < 2) return;
        
        const points = this.route3D.map(p => new THREE.Vector3(p.x, 0.1, p.y));
        const geometry = new THREE.BufferGeometry().setFromPoints(points);
        const material = new THREE.LineBasicMaterial({ color: 0x00AAFF, linewidth: 4 });
        
        this.currentRouteMesh = new THREE.Line(geometry, material);
        this.scene.add(this.currentRouteMesh);
    }

    /**
     * Update camera position to follow player
     */
    updateCameraPosition() {
        this.camera.position.copy(this.player.position);
        this.camera.position.y = this.PLAYER_HEIGHT;
        this.camera.position.z += 20;
        this.camera.lookAt(this.player.position.x, 0, this.player.position.z);
    }

    /**
     * Handle movement
     */
    handleMovement() {
        const moveDir = new THREE.Vector3();
        
        if (this.keys['w']) moveDir.z -= 1;
        if (this.keys['s']) moveDir.z += 1;
        if (this.keys['a']) moveDir.x -= 1;
        if (this.keys['d']) moveDir.x += 1;
        
        if (moveDir.length() > 0) {
            moveDir.normalize();
            this.player.position.addScaledVector(moveDir, this.MOVE_SPEED);
        }
    }

    /**
     * Animation loop
     */
    animate = () => {
        requestAnimationFrame(this.animate);
        
        this.handleMovement();
        this.updateCameraPosition();
        
        this.renderer.render(this.scene, this.camera);
    }

    /**
     * Handle window resize
     */
    onWindowResize() {
        const w = window.innerWidth;
        const h = window.innerHeight;
        this.camera.aspect = w / h;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(w, h);
    }
}

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', async () => {
    const nav3d = new DenahNavigation3D();
    await nav3d.initialize();
    window.denahNav3D = nav3d; // Expose for debugging
});

export { DenahNavigation3D };
