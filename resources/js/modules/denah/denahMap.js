/**
 * DenahMap - Main module untuk 2D denah interactivity
 * Orchestrate SVGRenderer, RouteCalculator, NavigationManager, dan EventBus
 */

import { SVGRenderer } from './svgRenderer.js';
import { RouteCalculator } from './routeCalculator.js';
import { NavigationManager } from './navigationMgr.js';
import EventBus from './eventBus.js';

export class DenahMap {
    constructor(options = {}) {
        this.options = {
            svgSelector: '#denahSvg',
            containerSelector: '.denah-wrap',
            infoCardSelector: '#infoCard',
            infoOverlaySelector: '#infoOverlay',
            ...options,
        };

        this.eventBus = new EventBus();
        this.svg = new SVGRenderer(this.options.svgSelector, this.options.containerSelector);
        this.router = new RouteCalculator();
        this.navigation = new NavigationManager(this.eventBus);
        this.infoCard = document.querySelector(this.options.infoCardSelector);
        this.infoOverlay = document.querySelector(this.options.infoOverlaySelector);

        this.setupEventListeners();
    }

    /**
     * Initialize denah map
     */
    initialize(tenantData) {
        this.navigation.initialize(tenantData);
        this.svg.parseNodes();
        this.svg.getInteractiveLapaks();
        
        this.checkUrlParams();
        this.setupLapakClickListeners();
        this.setupLegendFilterListeners();
        this.setupZoomControls();
    }

    /**
     * Setup internal event listeners untuk modules
     */
    setupEventListeners() {
        // Handle start node change
        this.eventBus.on('startNodeChanged', (data) => {
            console.log('Start node changed:', data.nodeId);
            this.updateStartNodeUI(data.nodeId);
        });

        // Handle target change
        this.eventBus.on('targetLapakChanged', async (data) => {
            await this.updateRoutePreview();
        });

        // Handle route update
        this.eventBus.on('routeUpdated', (data) => {
            if (data.route && data.route.path) {
                this.svg.drawRoute(data.route.path);
            }
        });
    }

    /**
     * Check URL params untuk deep-linking (QR code scan)
     */
    checkUrlParams() {
        const params = new URLSearchParams(window.location.search);
        const scanNode = params.get('scan_node');

        if (scanNode) {
            this.navigation.setStartNode(scanNode);
        }
    }

    /**
     * Setup click listeners untuk semua interactive lapak
     */
    setupLapakClickListeners() {
        this.svg.interactiveLapaks.forEach(lapak => {
            lapak.element.addEventListener('click', (e) => {
                e.stopPropagation();
                this.showLapakInfo(lapak.id, lapak.category);
            });
        });

        // Close info card saat klik di luar
        document.addEventListener('click', (e) => {
            if (this.infoCard && !this.infoCard.contains(e.target) && !e.target.closest('svg')) {
                this.closeInfoCard();
            }
        });
    }

    /**
     * Show info card untuk lapak yang diklik
     */
    showLapakInfo(lapakId, category) {
        const tenantInfo = this.navigation.getTenantInfo(lapakId);
        
        if (!tenantInfo) {
            console.warn('Tenant info not found for:', lapakId);
            return;
        }

        this.navigation.setTargetLapak(lapakId);

        // Update info card UI
        if (this.infoCard) {
            document.getElementById('infoId').textContent = lapakId;
            document.getElementById('infoTitle').textContent = tenantInfo.nama || 'Nama Toko';
            document.getElementById('infoDesc').textContent = tenantInfo.deskripsi || 'Belum ada informasi.';
            document.getElementById('infoBadge').textContent = (tenantInfo.kategori || category).toUpperCase();
            
            const infoImage = document.getElementById('infoImage');
            if (tenantInfo.foto) {
                infoImage.src = tenantInfo.foto;
            }

            // Setup buttons
            this.setupInfoCardButtons(lapakId);

            this.infoCard.classList.add('show');
            if (this.infoOverlay) this.infoOverlay.classList.remove('hidden');
        }
    }

    /**
     * Setup buttons dalam info card
     */
    setupInfoCardButtons(lapakId) {
        const btnPilihStart = document.getElementById('btnPilihStart');
        const btnNavigasi3d = document.getElementById('btnNavigasi3d');

        if (btnPilihStart) {
            btnPilihStart.onclick = () => {
                this.navigation.setStartNode(`node-${lapakId}`);
                const txtStart = document.getElementById('txtStartNode');
                if (txtStart) {
                    txtStart.textContent = this.navigation.getStartNodeLabel();
                }
            };
        }

        if (btnNavigasi3d) {
            btnNavigasi3d.onclick = () => {
                if (!this.navigation.startNodeId) {
                    alert('Silakan tentukan Lokasi Awal Anda terlebih dahulu!');
                    return;
                }

                sessionStorage.setItem('currentRoute', JSON.stringify(this.navigation.currentRoute));
                window.location.href = `/denah/rute?start=${this.navigation.startNodeId}&target=${lapakId}`;
            };
        }
    }

    /**
     * Update rute preview saat start atau target berubah
     */
    async updateRoutePreview() {
        if (!this.navigation.isReady()) {
            this.svg.clearRoute();
            return;
        }

        const route = await this.router.calculateRoute(
            this.navigation.startNodeId,
            this.navigation.targetLapakId,
            true // include coordinates
        );

        if (route) {
            this.navigation.setRoute(route);
        }
    }

    /**
     * Update start node UI saat berubah
     */
    updateStartNodeUI(nodeId) {
        // Highlight node di SVG
        this.svg.highlightNode(nodeId, { duration: 0 });

        // Update display text
        const txtStart = document.getElementById('txtStartNode');
        if (txtStart) {
            txtStart.textContent = this.navigation.getStartNodeLabel();
        }

        // Update route preview jika ada target
        if (this.navigation.targetLapakId) {
            this.updateRoutePreview();
        }
    }

    /**
     * Close info card
     */
    closeInfoCard() {
        if (this.infoCard) {
            this.infoCard.classList.remove('show');
        }
        if (this.infoOverlay) {
            this.infoOverlay.classList.add('hidden');
        }
    }

    /**
     * Setup legend filter listeners
     */
    setupLegendFilterListeners() {
        const legendButtons = document.querySelectorAll('.legend-item');
        
        legendButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                legendButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.dataset.filter;
                this.applyFilter(filter);
            });
        });
    }

    /**
     * Apply category filter
     */
    applyFilter(filter) {
        this.svg.interactiveLapaks.forEach(lapak => {
            if (filter === 'all' || lapak.category === filter) {
                this.svg.setLapakDimmed(lapak.id, false);
            } else {
                this.svg.setLapakDimmed(lapak.id, true);
            }
        });
    }

    /**
     * Setup zoom controls
     */
    setupZoomControls() {
        let scale = 1;
        const container = document.querySelector(this.options.containerSelector);
        const svg = document.querySelector(this.options.svgSelector);

        const applyZoom = () => {
            if (svg) svg.style.transform = `scale(${scale})`;
        };

        document.getElementById('btn-zoom-in')?.addEventListener('click', () => {
            scale = Math.min(3, parseFloat((scale + 0.3).toFixed(1)));
            applyZoom();
        });

        document.getElementById('btn-zoom-out')?.addEventListener('click', () => {
            scale = Math.max(0.5, parseFloat((scale - 0.3).toFixed(1)));
            applyZoom();
        });

        document.getElementById('btn-zoom-reset')?.addEventListener('click', () => {
            scale = 1;
            applyZoom();
            if (container) {
                container.scrollTop = 0;
                container.scrollLeft = 0;
            }
            this.closeInfoCard();
        });
    }
}

export default DenahMap;
