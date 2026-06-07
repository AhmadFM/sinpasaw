/**
 * NavigationManager - Manage navigation state dan logic
 * Track start point, target, rute, dan current location
 */

export class NavigationManager {
    constructor(eventBus) {
        this.eventBus = eventBus;
        this.startNodeId = null;
        this.targetLapakId = null;
        this.currentRoute = null;
        this.allTenantData = {};
    }

    /**
     * Initialize dengan tenant data
     */
    initialize(tenantData) {
        this.allTenantData = tenantData;
    }

    /**
     * Set start node dari URL param atau manual selection
     */
    setStartNode(nodeId) {
        if (this.startNodeId === nodeId) return;
        
        this.startNodeId = nodeId;
        this.eventBus.emit('startNodeChanged', { nodeId });
    }

    /**
     * Set target lapak
     */
    setTargetLapak(lapakId) {
        if (this.targetLapakId === lapakId) return;
        
        this.targetLapakId = lapakId;
        this.eventBus.emit('targetLapakChanged', { lapakId });
    }

    /**
     * Set current calculated route
     */
    setRoute(route) {
        this.currentRoute = route;
        this.eventBus.emit('routeUpdated', { route });
    }

    /**
     * Get tenant info for lapak
     */
    getTenantInfo(lapakId) {
        return this.allTenantData[lapakId] || null;
    }

    /**
     * Get start node label
     */
    getStartNodeLabel() {
        if (!this.startNodeId) return 'Belum ditentukan';
        
        // Convert node ID to readable label
        return this.startNodeId.replace('node-', '').replace(/_/g, ' ').toUpperCase();
    }

    /**
     * Reset navigation state
     */
    reset() {
        this.startNodeId = null;
        this.targetLapakId = null;
        this.currentRoute = null;
        this.eventBus.emit('navigationReset');
    }

    /**
     * Check if navigation is ready (has start and target)
     */
    isReady() {
        return this.startNodeId !== null && this.targetLapakId !== null;
    }

    /**
     * Get navigation state snapshot
     */
    getState() {
        return {
            startNodeId: this.startNodeId,
            targetLapakId: this.targetLapakId,
            currentRoute: this.currentRoute,
        };
    }
}

export default NavigationManager;
