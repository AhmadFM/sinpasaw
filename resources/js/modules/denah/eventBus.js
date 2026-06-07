/**
 * EventBus - Simple event coordination system
 * Untuk komunikasi antar modules tanpa tight coupling
 */

class EventBus {
    constructor() {
        this.events = {};
    }

    /**
     * Subscribe to an event
     * @param {string} eventName
     * @param {function} handler
     */
    on(eventName, handler) {
        if (!this.events[eventName]) {
            this.events[eventName] = [];
        }
        this.events[eventName].push(handler);
        
        // Return unsubscribe function
        return () => {
            this.events[eventName] = this.events[eventName].filter(h => h !== handler);
        };
    }

    /**
     * Emit an event
     * @param {string} eventName
     * @param {*} data
     */
    emit(eventName, data) {
        if (!this.events[eventName]) return;
        
        this.events[eventName].forEach(handler => {
            try {
                handler(data);
            } catch (error) {
                console.error(`Error in ${eventName} handler:`, error);
            }
        });
    }

    /**
     * Subscribe once and auto-unsubscribe
     */
    once(eventName, handler) {
        const wrappedHandler = (data) => {
            handler(data);
            unsubscribe();
        };
        
        const unsubscribe = this.on(eventName, wrappedHandler);
    }

    /**
     * Clear all event listeners
     */
    clear(eventName = null) {
        if (eventName) {
            delete this.events[eventName];
        } else {
            this.events = {};
        }
    }
}

export default EventBus;
