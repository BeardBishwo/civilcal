/**
 * Test Setup and Configuration
 * Global test configuration for Bishwo Calculator API tests
 */

const axios = require('axios');

// Global test configuration
global.testConfig = {
    baseURL: 'http://localhost:8000',
    timeout: 10000,
    adminCredentials: {
        email: 'uniquebishwo@gmail.com',
        password: 'c9PU7XAsAADYk_A'
    }
};

// Configure axios defaults
axios.defaults.timeout = global.testConfig.timeout;
axios.defaults.validateStatus = function (status) {
    return status >= 200 && status < 500; // Don't throw for 4xx errors, handle them in tests
};

// Global test utilities
global.TestHelper = {
    /**
     * Create authenticated client for admin operations
     */
    async createAdminClient() {
        const client = axios.create({
            baseURL: global.testConfig.baseURL,
            withCredentials: true,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        // Get CSRF token from meta tag in HTML (same way frontend does it)
        const homeResponse = await client.get('/');
        let csrfToken = '';

        // Try to extract from HTML meta tag first
        if (homeResponse.data && typeof homeResponse.data === 'string') {
            const metaMatch = homeResponse.data.match(/<meta[^>]*name="csrf-token"[^>]*content="([^"]+)"/i);
            if (metaMatch && metaMatch[1]) {
                csrfToken = metaMatch[1];
            }
        }

        // Fallback to header if meta tag extraction fails
        if (!csrfToken && homeResponse.headers['x-csrf-token']) {
            csrfToken = homeResponse.headers['x-csrf-token'];
        }

        // Login as admin
        const loginResponse = await client.post('/api/login', {
            username_email: global.testConfig.adminCredentials.email,
            password: global.testConfig.adminCredentials.password
        }, {
            headers: csrfToken ? { 'X-CSRF-Token': csrfToken } : {}
        });

        if (loginResponse.status !== 200 || !loginResponse.data.success) {
            throw new Error('Admin login failed: ' + JSON.stringify(loginResponse.data));
        }

        // Store CSRF token for future requests
        client.csrfToken = csrfToken;
        client.isAdmin = true;

        return client;
    },

    /**
     * Create regular user client
     */
    async createUserClient(userCredentials = null) {
        const client = axios.create({
            baseURL: global.testConfig.baseURL,
            withCredentials: true,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (userCredentials) {
            // Get CSRF token from meta tag in HTML (same way frontend does it)
            const homeResponse = await client.get('/');
            let csrfToken = '';

            // Try to extract from HTML meta tag first
            if (homeResponse.data && typeof homeResponse.data === 'string') {
                const metaMatch = homeResponse.data.match(/<meta[^>]*name="csrf-token"[^>]*content="([^"]+)"/i);
                if (metaMatch && metaMatch[1]) {
                    csrfToken = metaMatch[1];
                }
            }

            // Fallback to header if meta tag extraction fails
            if (!csrfToken && homeResponse.headers['x-csrf-token']) {
                csrfToken = homeResponse.headers['x-csrf-token'];
            }

            // Login as user
            const loginResponse = await client.post('/api/login', {
                username_email: userCredentials.email || userCredentials.username,
                password: userCredentials.password
            }, {
                headers: csrfToken ? { 'X-CSRF-Token': csrfToken } : {}
            });

            if (loginResponse.status !== 200 || !loginResponse.data.success) {
                throw new Error('User login failed: ' + JSON.stringify(loginResponse.data));
            }

            client.csrfToken = csrfToken;
            client.isUser = true;
        }

        return client;
    },

    /**
     * Generate unique test data
     */
    generateTestData(type = 'user') {
        const timestamp = Date.now();
        const random = Math.random().toString(36).substring(2, 8);

        switch (type) {
            case 'user':
                return {
                    username: `testuser_${timestamp}_${random}`,
                    email: `testuser_${timestamp}_${random}@example.com`,
                    password: 'TestPass123!',
                    first_name: 'Test',
                    last_name: 'User'
                };

            case 'calculator':
                return {
                    length: Math.floor(Math.random() * 100) + 1,
                    width: Math.floor(Math.random() * 50) + 1,
                    height: Math.floor(Math.random() * 20) + 1,
                    brick_size: 'standard',
                    mortar_thickness: Math.floor(Math.random() * 10) + 5
                };

            default:
                return {};
        }
    },

    /**
     * Clean up test data
     */
    async cleanupTestData(client, testData) {
        // Implementation for cleaning up test data
        // This would depend on available cleanup endpoints
        try {
            if (client.isAdmin && testData.users) {
                // Admin cleanup logic
            }
        } catch (error) {
            console.warn('Cleanup failed:', error.message);
        }
    },

    /**
     * Wait for specified milliseconds
     */
    async wait(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    },

    /**
     * Retry operation with exponential backoff
     */
    async retry(operation, maxRetries = 3, baseDelay = 1000) {
        let lastError;

        for (let attempt = 1; attempt <= maxRetries; attempt++) {
            try {
                return await operation();
            } catch (error) {
                lastError = error;

                if (attempt === maxRetries) {
                    throw lastError;
                }

                const delay = baseDelay * Math.pow(2, attempt - 1);
                await this.wait(delay);
            }
        }
    }
};

// Test lifecycle hooks
before(async function() {
    console.log('🚀 Starting Bishwo Calculator API Test Suite');
    console.log('📍 Base URL:', global.testConfig.baseURL);
    console.log('⏱️  Timeout:', global.testConfig.timeout + 'ms');
    console.log('');

    // Verify server is running
    try {
        await axios.get(global.testConfig.baseURL + '/api/v1/health', { timeout: 5000 });
        console.log('✅ Server is running and healthy');
    } catch (error) {
        console.error('❌ Server is not responding:', error.message);
        console.error('Please ensure the PHP development server is running on', global.testConfig.baseURL);
        process.exit(1);
    }
});

after(async function() {
    console.log('');
    console.log('🏁 Test suite completed');

    const stats = {
        suites: this.test.parent.suites.length,
        tests: this.test.parent.tests.length,
        passes: this.test.parent.passes.length,
        failures: this.test.parent.failures.length,
        duration: Date.now() - this.test.parent.startTime
    };

    console.log(`📊 Results: ${stats.passes}/${stats.tests} tests passed`);
    console.log(`⏱️  Duration: ${Math.round(stats.duration / 1000)}s`);

    if (stats.failures > 0) {
        console.log('❌ Failed tests:');
        if (this.test && this.test.parent && this.test.parent.failures) {
            this.test.parent.failures.forEach((failure, index) => {
                console.log(`   ${index + 1}. ${failure.title}`);
                console.log(`      ${failure.err.message}`);
            });
        } else {
            console.log('   Unable to retrieve failure details');
        }
    } else {
        console.log('🎉 All tests passed!');
    }
});

// Individual test hooks
beforeEach(function() {
    // Reset axios defaults for each test
    axios.defaults.timeout = global.testConfig.timeout;
});

afterEach(function() {
    // Clean up after each test if needed
    // This can be customized based on test requirements
});

// Export for use in other test files
module.exports = {
    testConfig: global.testConfig,
    TestHelper: global.TestHelper
};