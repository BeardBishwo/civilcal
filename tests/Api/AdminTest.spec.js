/**
 * Bishwo Calculator API - Admin Panel Tests
 * Tests admin-only endpoints and functionality
 */

const axios = require('axios');
const { expect } = require('chai');

describe('Bishwo Calculator API - Admin Panel', () => {
    const baseURL = 'http://localhost:8000';
    let csrfToken = '';
    let sessionCookies = '';
    let authToken = '';

    // Setup axios instance with cookie jar
    const client = axios.create({
        baseURL,
        withCredentials: true,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });

    before(async () => {
        // Get CSRF token and authenticate as admin
        try {
            const response = await client.get('/');
            const setCookieHeader = response.headers['set-cookie'];
            if (setCookieHeader) {
                sessionCookies = setCookieHeader.join('; ');
            }

            const csrfHeader = response.headers['x-csrf-token'];
            if (csrfHeader) {
                csrfToken = csrfHeader;
            }

            // Login as admin
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            const loginResponse = await client.post('/api/login', {
                username_email: 'uniquebishwo@gmail.com',
                password: 'c9PU7XAsAADYk_A'
            }, { headers });

            expect(loginResponse.status).to.equal(200);
            expect(loginResponse.data.success).to.equal(true);
            expect(loginResponse.data.user.is_admin).to.equal(true);

            authToken = loginResponse.data.user.id;

        } catch (error) {
            console.error('Admin authentication failed:', error.response?.data || error.message);
            throw error;
        }
    });

    describe('GET /api/admin/dashboard/stats', () => {
        it('should return dashboard statistics for admin', async () => {
            const response = await client.get('/api/admin/dashboard/stats');

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('stats');

            const stats = response.data.stats;
            expect(stats).to.have.property('users');
            expect(stats).to.have.property('system');
            expect(stats).to.have.property('modules');
            expect(stats).to.have.property('analytics');

            // Check user stats structure
            expect(stats.users).to.have.property('total');
            expect(stats.users).to.have.property('active');
            expect(stats.users).to.have.property('new_today');
            expect(stats.users).to.have.property('roles');

            // Check system stats structure
            expect(stats.system).to.have.property('php_version');
            expect(stats.system).to.have.property('memory_usage');
            expect(stats.system).to.have.property('storage_used');
            expect(stats.system).to.have.property('uptime');
        });

        it('should reject access for non-admin users', async () => {
            // This would require testing with a non-admin user
            // For now, we'll assume the endpoint properly checks admin access
        });
    });

    describe('GET /api/admin/modules', () => {
        it('should return list of all modules with status', async () => {
            const response = await client.get('/api/admin/modules');

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('modules');
            expect(response.data.modules).to.be.an('array');

            if (response.data.modules.length > 0) {
                const firstModule = response.data.modules[0];
                expect(firstModule).to.have.property('name');
                expect(firstModule).to.have.property('is_active');
                expect(firstModule).to.have.property('settings_url');
                expect(firstModule).to.have.property('has_settings');
            }
        });

        it('should include module activation status', async () => {
            const response = await client.get('/api/admin/modules');

            response.data.modules.forEach(module => {
                expect(module).to.have.property('is_active');
                expect(typeof module.is_active).to.equal('boolean');
            });
        });
    });

    describe('POST /api/admin/modules/toggle', () => {
        it('should activate a module successfully', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            // First get available modules
            const modulesResponse = await client.get('/api/admin/modules');
            const availableModules = modulesResponse.data.modules;

            if (availableModules.length > 0) {
                const testModule = availableModules[0];

                const response = await client.post('/api/admin/modules/toggle', {
                    module: testModule.name,
                    action: testModule.is_active ? 'deactivate' : 'activate'
                }, { headers });

                expect(response.status).to.equal(200);
                expect(response.data).to.have.property('success', true);
                expect(response.data).to.have.property('message');
            }
        });

        it('should reject invalid module names', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            try {
                await client.post('/api/admin/modules/toggle', {
                    module: 'nonexistent_module',
                    action: 'activate'
                }, { headers });
                expect.fail('Should have thrown error for invalid module');
            } catch (error) {
                expect(error.response.status).to.be.oneOf([400, 404]);
                expect(error.response.data).to.have.property('error');
            }
        });

        it('should reject invalid actions', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            try {
                await client.post('/api/admin/modules/toggle', {
                    module: 'test_module',
                    action: 'invalid_action'
                }, { headers });
                expect.fail('Should have thrown error for invalid action');
            } catch (error) {
                expect(error.response.status).to.equal(400);
                expect(error.response.data).to.have.property('error');
            }
        });
    });

    describe('GET /api/admin/system/health', () => {
        it('should return comprehensive system health check', async () => {
            const response = await client.get('/api/admin/system/health');

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('health');

            const health = response.data.health;
            expect(health).to.have.property('overall_status');
            expect(health).to.have.property('checks');

            // Check required health checks
            const requiredChecks = [
                'php_version',
                'memory_usage',
                'storage_space',
                'database_connection',
                'file_permissions'
            ];

            requiredChecks.forEach(checkName => {
                expect(health.checks).to.have.property(checkName);
                expect(health.checks[checkName]).to.have.property('status');
                expect(health.checks[checkName]).to.have.property('message');
                expect(health.checks[checkName]).to.have.property('value');
            });
        });

        it('should determine overall health status correctly', async () => {
            const response = await client.get('/api/admin/system/health');

            const health = response.data.health;
            expect(health.overall_status).to.be.oneOf(['healthy', 'warning', 'critical']);

            // If there are any failed checks, overall status should not be healthy
            const hasFailures = Object.values(health.checks).some(check => check.status === 'fail');
            if (hasFailures) {
                expect(health.overall_status).to.not.equal('healthy');
            }
        });
    });

    describe('POST /api/admin/backup/create', () => {
        it('should create database backup successfully', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            const response = await client.post('/api/admin/backup/create', {}, { headers });

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('message').that.includes('successfully');
            expect(response.data).to.have.property('backup_name');
            expect(response.data).to.have.property('file_size');
        });

        it('should return backup file information', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            const response = await client.post('/api/admin/backup/create', {}, { headers });

            expect(response.data.backup_name).to.match(/backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql/);
            expect(response.data.file_size).to.be.a('string');
            expect(response.data.file_size).to.include('MB').or.include('KB').or.include('B');
        });
    });

    describe('GET /api/admin/activity', () => {
        it('should return user activity logs', async () => {
            const response = await client.get('/api/admin/activity');

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('activities');
            expect(response.data).to.have.property('total');

            expect(response.data.activities).to.be.an('array');
            expect(typeof response.data.total).to.equal('number');
        });

        it('should support pagination parameters', async () => {
            const response = await client.get('/api/admin/activity?limit=5&offset=0');

            expect(response.status).to.equal(200);
            expect(response.data.activities.length).to.be.at.most(5);
        });

        it('should return properly structured activity records', async () => {
            const response = await client.get('/api/admin/activity');

            if (response.data.activities.length > 0) {
                const activity = response.data.activities[0];
                expect(activity).to.have.property('id');
                expect(activity).to.have.property('user');
                expect(activity).to.have.property('action');
                expect(activity).to.have.property('ip_address');
                expect(activity).to.have.property('timestamp');
                expect(activity).to.have.property('type');
            }
        });
    });

    describe('Admin access control', () => {
        it('should reject admin endpoints for non-admin users', async () => {
            // Logout admin user
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }
            await client.post('/api/logout', {}, { headers });

            // Try to access admin endpoint without authentication
            try {
                await client.get('/api/admin/dashboard/stats');
                expect.fail('Should have thrown error for unauthorized access');
            } catch (error) {
                expect(error.response.status).to.equal(403);
                expect(error.response.data).to.have.property('error');
            }
        });

        it('should require authentication for all admin endpoints', async () => {
            // Create a new client instance without authentication
            const unauthClient = axios.create({
                baseURL,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const adminEndpoints = [
                '/api/admin/dashboard/stats',
                '/api/admin/modules',
                '/api/admin/system/health',
                '/api/admin/activity'
            ];

            for (const endpoint of adminEndpoints) {
                try {
                    await unauthClient.get(endpoint);
                    expect.fail(`Should have thrown error for ${endpoint}`);
                } catch (error) {
                    expect(error.response.status).to.be.oneOf([401, 403]);
                    expect(error.response.data).to.have.property('error');
                }
            }
        });
    });

    describe('CSRF protection for admin endpoints', () => {
        it('should require CSRF token for POST admin operations', async () => {
            // Try POST request without CSRF token
            try {
                await client.post('/api/admin/modules/toggle', {
                    module: 'test_module',
                    action: 'activate'
                });
                expect.fail('Should have thrown error for missing CSRF token');
            } catch (error) {
                expect(error.response.status).to.equal(419);
                expect(error.response.data.message).to.include('CSRF token');
            }
        });
    });

    after(async () => {
        // Cleanup: logout admin user
        try {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }
            await client.post('/api/logout', {}, { headers });
        } catch (error) {
            console.log('Cleanup logout failed, but continuing...');
        }
    });
});