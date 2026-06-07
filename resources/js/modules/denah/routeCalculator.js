/**
 * RouteCalculator - Frontend wrapper untuk Dijkstra routing
 * Komunikasi dengan backend API untuk calculate rute
 */

export class RouteCalculator {
    constructor(apiEndpoint = '/api/denah/route') {
        this.apiEndpoint = apiEndpoint;
        this.lastRoute = null;
        this.isCalculating = false;
    }

    /**
     * Calculate rute dari start ke end node
     * @param {string} startNodeId - Format: 'node-XXX'
     * @param {string} endLapakId - Format: 'LXX' (lapak ID)
     * @param {boolean} includeCoordinates - Include x,y dalam response
     */
    async calculateRoute(startNodeId, endLapakId, includeCoordinates = true) {
        if (this.isCalculating) {
            console.warn('Route calculation already in progress');
            return null;
        }

        this.isCalculating = true;

        try {
            const endNodeId = `node-${endLapakId}`;
            
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
                body: JSON.stringify({
                    start_node_id: startNodeId,
                    end_node_id: endNodeId,
                    include_coordinates: includeCoordinates,
                }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success && result.data) {
                this.lastRoute = result.data;
                return result.data;
            } else {
                throw new Error(result.message || 'Failed to calculate route');
            }
        } catch (error) {
            console.error('Error calculating route:', error);
            return null;
        } finally {
            this.isCalculating = false;
        }
    }

    /**
     * Get cached last route
     */
    getLastRoute() {
        return this.lastRoute;
    }

    /**
     * Get CSRF token dari meta tag
     */
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    /**
     * Format distance untuk display
     */
    formatDistance(distance) {
        if (distance < 1000) {
            return `${Math.round(distance)} m`;
        } else {
            return `${(distance / 1000).toFixed(2)} km`;
        }
    }

    /**
     * Estimate waktu tempuh (asumsi kecepatan rata-rata 1.4 m/s = 5 km/h)
     */
    estimateWalkingTime(distance) {
        const speedMs = 1.4; // meter per second
        const timeSeconds = distance / speedMs;
        const minutes = Math.round(timeSeconds / 60);
        return minutes;
    }
}

export default RouteCalculator;
