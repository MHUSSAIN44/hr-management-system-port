// resources/js/utils/locationService.js

export class LocationService {
    constructor() {
        this.watchId = null;
        this.lastPosition = null;
    }

    // Check if geolocation is supported
    static isSupported() {
        return 'geolocation' in navigator;
    }

    // Check location permissions
    static async checkPermissions() {
        if (!('permissions' in navigator)) {
            return 'unsupported';
        }

        try {
            const result = await navigator.permissions.query({ name: 'geolocation' });
            return result.state; // 'granted', 'denied', or 'prompt'
        } catch (error) {
            return 'error';
        }
    }

    // Get current position with high accuracy
    async getCurrentPosition(options = {}) {
        return new Promise((resolve, reject) => {
            if (!LocationService.isSupported()) {
                reject(new Error('Geolocation is not supported by this browser.'));
                return;
            }

            const defaultOptions = {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 0,
                ...options
            };

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    try {
                        const { latitude, longitude, accuracy } = position.coords;
                        const address = await this.getAddressFromCoordinates(latitude, longitude);

                        const locationData = {
                            latitude,
                            longitude,
                            accuracy,
                            address,
                            timestamp: position.timestamp
                        };

                        this.lastPosition = locationData;
                        resolve(locationData);
                    } catch (error) {
                        reject(error);
                    }
                },
                (error) => {
                    let errorMessage = 'Unable to get your location.';

                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access denied. Please enable location permissions in your browser settings.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information is unavailable. Please check your GPS settings.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out. Please try again.';
                            break;
                    }

                    reject(new Error(errorMessage));
                },
                defaultOptions
            );
        });
    }

    // Watch position changes (for live tracking)
    watchPosition(callback, options = {}) {
        if (!LocationService.isSupported()) {
            throw new Error('Geolocation is not supported by this browser.');
        }

        const defaultOptions = {
            enableHighAccuracy: true,
            timeout: 30000,
            maximumAge: 60000, // 1 minute
            ...options
        };

        this.watchId = navigator.geolocation.watchPosition(
            (position) => {
                const { latitude, longitude, accuracy } = position.coords;
                const locationData = {
                    latitude,
                    longitude,
                    accuracy,
                    timestamp: position.timestamp
                };

                this.lastPosition = locationData;
                callback(locationData);
            },
            (error) => {
                callback(null, error);
            },
            defaultOptions
        );

        return this.watchId;
    }

    // Stop watching position
    stopWatching() {
        if (this.watchId !== null) {
            navigator.geolocation.clearWatch(this.watchId);
            this.watchId = null;
        }
    }

    // Get address from coordinates using reverse geocoding
    async getAddressFromCoordinates(lat, lng) {
        try {
            // Option 1: Use OpenCage Geocoding API (free tier available)
            const response = await fetch(
                `https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=5d01ea6d9065400381fd1a688770790e&language=en&pretty=1`
            );
            const data = await response.json();

            if (data.results && data.results.length > 0) {
                return data.results[0].formatted;
            }

            // Option 2: Fallback to coordinates
            return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

        } catch (error) {
            console.warn('Failed to get address:', error);
            return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    }

    // Calculate distance between two points (Haversine formula)
    static calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Earth's radius in meters
        const dLat = this.toRadians(lat2 - lat1);
        const dLon = this.toRadians(lon2 - lon1);
        const a =
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(this.toRadians(lat1)) * Math.cos(this.toRadians(lat2)) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c; // Distance in meters
    }

    static toRadians(degrees) {
        return degrees * (Math.PI/180);
    }

    // Validate location accuracy
    static isAccuracyAcceptable(accuracy, threshold = 50) {
        return accuracy <= threshold;
    }

    // Check if device has high accuracy enabled
    static async testLocationAccuracy() {
        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            });

            return {
                accuracy: position.coords.accuracy,
                isHighAccuracy: position.coords.accuracy <= 20
            };
        } catch (error) {
            throw new Error('Failed to test location accuracy');
        }
    }

    // Get mock location detection (basic)
    static isMockLocationEnabled(position) {
        // This is a basic check and may not catch all mock locations
        // More sophisticated detection would require native app features

        if (position.coords.accuracy === 0 || position.coords.accuracy === 1) {
            return true; // Suspiciously perfect accuracy
        }

        if (position.coords.speed !== null && position.coords.speed === 0 &&
            position.coords.heading !== null && position.coords.heading === 0) {
            return true; // Suspicious combination
        }

        return false; // Cannot determine with certainty in web
    }

    // Request location permissions
    static async requestPermissions() {
        if (!LocationService.isSupported()) {
            throw new Error('Geolocation is not supported');
        }

        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: false,
                    timeout: 5000,
                    maximumAge: 300000 // 5 minutes
                });
            });

            return 'granted';
        } catch (error) {
            if (error.code === error.PERMISSION_DENIED) {
                throw new Error('Location permission denied');
            }
            throw error;
        }
    }
}

// Export as default
export default LocationService;