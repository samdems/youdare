import axios from 'axios';

// Create axios instance with default config
const apiClient = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    timeout: 30000, // 30 seconds
});

// Request interceptor - add CSRF token to all requests
apiClient.interceptors.request.use(
    (config) => {
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor - handle errors globally
apiClient.interceptors.response.use(
    (response) => {
        // Return the data directly if it exists
        return response.data;
    },
    (error) => {
        // Handle different error types
        if (error.response) {
            // Server responded with error status
            const { status, data } = error.response;

            switch (status) {
                case 401:
                    console.error('Unauthorized - please log in');
                    break;
                case 403:
                    console.error('Forbidden - you do not have permission');
                    break;
                case 404:
                    console.error('Not found');
                    break;
                case 422:
                    console.error('Validation error:', data.errors || data.message);
                    break;
                case 500:
                    console.error('Server error - please try again later');
                    break;
                default:
                    console.error('API error:', data.message || 'Unknown error');
            }

            // Return a normalized error object
            return Promise.reject({
                status,
                message: data.message || 'An error occurred',
                errors: data.errors || null,
                data: data,
            });
        } else if (error.request) {
            // Request was made but no response received
            console.error('Network error - please check your connection');
            return Promise.reject({
                status: 0,
                message: 'Network error - please check your connection',
                errors: null,
            });
        } else {
            // Something else happened
            console.error('Error:', error.message);
            return Promise.reject({
                status: 0,
                message: error.message || 'An unexpected error occurred',
                errors: null,
            });
        }
    }
);

export default apiClient;
