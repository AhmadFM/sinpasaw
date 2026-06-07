/**
 * denah-main.js - Entry point untuk denah 2D map
 * Initialize dan setup semua modules
 */

import DenahMap from './modules/denah/denahMap.js';

/**
 * Initialize denah map dengan tenant data
 */
export function initDenahMap(tenantData) {
    const denahMap = new DenahMap({
        svgSelector: '#denahSvg',
        containerSelector: '.denah-wrap',
        infoCardSelector: '#infoCard',
        infoOverlaySelector: '#infoOverlay',
    });

    // Initialize dengan data
    denahMap.initialize(tenantData);

    // Expose ke window untuk debugging
    window.denahMapInstance = denahMap;

    console.log('✓ Denah Map initialized successfully');
}

export default initDenahMap;
