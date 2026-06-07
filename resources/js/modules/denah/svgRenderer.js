/**
 * SVGRenderer - Handle SVG rendering dan interaksi
 * Extract SVG nodes, setup event listeners, manage visual states
 */

export class SVGRenderer {
    constructor(svgSelector = '#denahSvg', containerSelector = '.denah-wrap') {
        this.svg = document.querySelector(svgSelector);
        this.container = document.querySelector(containerSelector);
        this.nodes = [];
        this.interactiveLapaks = [];
    }

    /**
     * Parse SVG dan extract semua nodes (circle elements)
     */
    parseNodes() {
        if (!this.svg) return [];
        
        const circles = this.svg.querySelectorAll('circle[id^="node-"]');
        this.nodes = Array.from(circles).map(circle => ({
            id: circle.getAttribute('id'),
            x: parseFloat(circle.getAttribute('cx')) || 0,
            y: parseFloat(circle.getAttribute('cy')) || 0,
            element: circle,
        }));
        
        return this.nodes;
    }

    /**
     * Get all interactive lapak elements (clickable areas)
     */
    getInteractiveLapaks() {
        if (!this.svg) return [];
        
        const interactiveClasses = [
            'lapak-sayur-buah-dan-jajanan',
            'lapak-olahan-dan-jajanan',
            'lapak-non-halal',
            'lapak-basah',
            'lapak-kuliner',
            'kios-besar',
            'kios-kecil',
            'kios-fnb',
            'atm',
            'mushola',
            'toilet',
        ];

        const selector = interactiveClasses.map(c => '.' + c).join(',');
        this.interactiveLapaks = Array.from(this.svg.querySelectorAll(selector)).map(el => ({
            id: el.id,
            element: el,
            category: el.classList[0],
        }));
        
        return this.interactiveLapaks;
    }

    /**
     * Highlight node (visual feedback saat select)
     */
    highlightNode(nodeId, options = {}) {
        const node = this.nodes.find(n => n.id === nodeId);
        if (!node) return;
        
        const {
            color = '#007E43',
            radius = 12,
            opacity = 1,
            duration = 0
        } = options;

        node.element.setAttribute('fill', color);
        node.element.setAttribute('r', radius);
        node.element.style.opacity = opacity;

        // Auto-restore after duration if specified
        if (duration > 0) {
            setTimeout(() => this.restoreNode(nodeId), duration);
        }
    }

    /**
     * Restore node to original state
     */
    restoreNode(nodeId) {
        const node = this.nodes.find(n => n.id === nodeId);
        if (!node) return;
        
        // Reset ke style original (perlu data original)
        node.element.style.opacity = '0';
        node.element.setAttribute('r', '6.12659');
    }

    /**
     * Draw rute path di SVG menggunakan polyline
     */
    drawRoute(pathPoints, options = {}) {
        const {
            color = '#00AAFF',
            width = 8,
            opacity = 0.9,
            polylineId = 'dijkstraPath'
        } = options;

        let polyline = this.svg.getElementById(polylineId);
        if (!polyline) {
            polyline = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
            polyline.setAttribute('id', polylineId);
            polyline.setAttribute('fill', 'none');
            polyline.setAttribute('stroke-linecap', 'round');
            polyline.setAttribute('stroke-linejoin', 'round');
            polyline.style.pointerEvents = 'none';
            polyline.style.filter = 'drop-shadow(0px 0px 8px ' + color + ')';
            this.svg.appendChild(polyline);
        }

        polyline.setAttribute('stroke', color);
        polyline.setAttribute('stroke-width', width);
        polyline.style.opacity = opacity;

        if (pathPoints && pathPoints.length > 0) {
            const pointsString = pathPoints.map(p => `${p.x},${p.y}`).join(' ');
            polyline.setAttribute('points', pointsString);
        } else {
            polyline.setAttribute('points', '');
        }
    }

    /**
     * Clear rute path
     */
    clearRoute(polylineId = 'dijkstraPath') {
        const polyline = this.svg.getElementById(polylineId);
        if (polyline) {
            polyline.setAttribute('points', '');
        }
    }

    /**
     * Apply dimming filter ke lapak (untuk filter kategori)
     */
    setLapakDimmed(lapakId, isDimmed = true) {
        const lapak = this.interactiveLapaks.find(l => l.id === lapakId);
        if (!lapak) return;
        
        if (isDimmed) {
            lapak.element.classList.add('lapak-dimmed');
        } else {
            lapak.element.classList.remove('lapak-dimmed');
        }
    }

    /**
     * Get screen-to-SVG coordinate transformation
     */
    getScreenToSvgTransform() {
        if (!this.svg) return null;
        return this.svg.getScreenCTM().inverse();
    }

    /**
     * Convert screen coordinates to SVG coordinates
     */
    screenToSvgCoords(screenX, screenY) {
        if (!this.svg) return { x: 0, y: 0 };
        
        const pt = this.svg.createSVGPoint();
        pt.x = screenX;
        pt.y = screenY;
        
        const svgPt = pt.matrixTransform(this.getScreenToSvgTransform());
        return { x: svgPt.x, y: svgPt.y };
    }
}

export default SVGRenderer;
