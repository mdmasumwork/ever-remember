class HttpService {
    static init() {
        this.setupAjaxDefaults();
    }

    static getHeaders() {
        const token = $('meta[name="csrf-token"]').attr('content');
        const headers = {
            'Content-Type': 'application/json'
        };

        if (token) {
            headers['X-CSRF-TOKEN'] = token;
        }

        return headers;
    }

    static setupAjaxDefaults() {
        $.ajaxSetup({
            headers: this.getHeaders()
        });
    }

    static async get(url) {
        return this.request(url, { method: 'GET' });
    }

    static async post(url, data) {
        return this.request(url, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    static async request(url, options = {}) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    ...this.getHeaders(),
                    ...options.headers
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    }
}