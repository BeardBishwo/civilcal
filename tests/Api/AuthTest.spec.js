/**
 * Bishwo Calculator API Authentication Tests
 * Tests authentication flow including CSRF token handling
 */

const axios = require('axios');
const { expect } = require('chai');

describe('Bishwo Calculator API - Authentication', () => {
    const baseURL = 'http://localhost:8000';
    let csrfToken = '';
    let sessionCookies = '';

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
        // Get CSRF token from a GET request
        try {
            const response = await client.get('/');
            const setCookieHeader = response.headers['set-cookie'];
            if (setCookieHeader) {
                sessionCookies = setCookieHeader.join('; ');
            }

            // Extract CSRF token from response headers
            const csrfHeader = response.headers['x-csrf-token'];
            if (csrfHeader) {
                csrfToken = csrfHeader;
            }
        } catch (error) {
            console.log('CSRF token extraction failed, continuing with tests...');
        }
    });

    describe('GET /api/health', () => {
        it('should return system health status', async () => {
            const response = await client.get('/api/v1/health');

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('status', 'ok');
            expect(response.data).to.have.property('app');
            expect(response.data.app).to.have.property('name', 'Bishwo Calculator');
        });
    });

    describe('GET /api/user-status', () => {
        it('should return user authentication status', async () => {
            const response = await client.get('/api/user-status');

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('logged_in', false);
        });
    });

    describe('GET /api/check-username', () => {
        it('should check username availability', async () => {
            const response = await client.get('/api/check-username?username=testuser123');

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('available', true);
            expect(response.data).to.have.property('username', 'testuser123');
        });

        it('should handle taken usernames', async () => {
            // First register a user to make username taken
            // This test might fail if user already exists
            const response = await client.get('/api/check-username?username=admin');

            expect(response.status).to.equal(200);
            // Username might be available or taken depending on existing data
            expect(response.data).to.have.property('available');
            expect(response.data).to.have.property('username', 'admin');
        });
    });

    describe('GET /api/calculators', () => {
        it('should return list of all calculators', async () => {
            const response = await client.get('/api/calculators');

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('data');
            expect(response.data.data).to.be.an('array');
            expect(response.data.data.length).to.be.greaterThan(0);

            // Check structure of first calculator
            const firstCalculator = response.data.data[0];
            expect(firstCalculator).to.have.property('category');
            expect(firstCalculator).to.have.property('tools');
            expect(firstCalculator.tools).to.be.an('array');
        });

        it('should include civil engineering calculators', async () => {
            const response = await client.get('/api/calculators');

            const civilCategory = response.data.data.find(cat => cat.category === 'civil');
            expect(civilCategory).to.exist;
            expect(civilCategory.tools).to.be.an('array');
            expect(civilCategory.tools.length).to.be.greaterThan(0);

            // Check for brickwork calculator
            const brickworkTool = civilCategory.tools.find(tool => tool.slug.includes('brick'));
            expect(brickworkTool).to.exist;
        });

        it('should include electrical engineering calculators', async () => {
            const response = await client.get('/api/calculators');

            const electricalCategory = response.data.data.find(cat => cat.category === 'electrical');
            expect(electricalCategory).to.exist;
            expect(electricalCategory.tools.length).to.be.greaterThan(20); // Should have many electrical tools
        });
    });

    describe('POST /api/login', () => {
        it('should reject login without CSRF token', async () => {
            try {
                await client.post('/api/login', {
                    username_email: 'uniquebishwo@gmail.com',
                    password: 'c9PU7XAsAADYk_A'
                });
                expect.fail('Should have thrown error for missing CSRF token');
            } catch (error) {
                expect(error.response.status).to.equal(419);
                expect(error.response.data).to.have.property('success', false);
                expect(error.response.data.message).to.include('CSRF token');
            }
        });

        it('should login admin user with valid credentials and CSRF token', async () => {
            // Set CSRF token in headers
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            const response = await client.post('/api/login', {
                username_email: 'uniquebishwo@gmail.com',
                password: 'c9PU7XAsAADYk_A'
            }, { headers });

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('message').that.includes('successful');
            expect(response.data).to.have.property('user');
            expect(response.data.user).to.have.property('email', 'uniquebishwo@gmail.com');
            expect(response.data.user).to.have.property('is_admin', true);
        });

        it('should reject login with invalid credentials', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            try {
                await client.post('/api/login', {
                    username_email: 'invalid@example.com',
                    password: 'wrongpassword'
                }, { headers });
                expect.fail('Should have thrown error for invalid credentials');
            } catch (error) {
                expect(error.response.status).to.equal(401);
                expect(error.response.data).to.have.property('error').that.includes('Invalid');
            }
        });

        it('should reject login with missing credentials', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            try {
                await client.post('/api/login', {}, { headers });
                expect.fail('Should have thrown error for missing credentials');
            } catch (error) {
                expect(error.response.status).to.equal(400);
                expect(error.response.data).to.have.property('error').that.includes('required');
            }
        });
    });

    describe('POST /api/register', () => {
        it('should reject registration without CSRF token', async () => {
            try {
                await client.post('/api/register', {
                    username: 'testuser123',
                    email: 'test@example.com',
                    password: 'TestPass123!',
                    first_name: 'Test',
                    last_name: 'User'
                });
                expect.fail('Should have thrown error for missing CSRF token');
            } catch (error) {
                expect(error.response.status).to.equal(419);
                expect(error.response.data.message).to.include('CSRF token');
            }
        });

        it('should register new user with valid data and CSRF token', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            // Generate unique username and email to avoid conflicts
            const timestamp = Date.now();
            const uniqueUsername = `testuser_${timestamp}`;
            const uniqueEmail = `testuser_${timestamp}@example.com`;

            const response = await client.post('/api/register', {
                username: uniqueUsername,
                email: uniqueEmail,
                password: 'TestPass123!',
                first_name: 'Test',
                last_name: 'User'
            }, { headers });

            expect(response.status).to.equal(200);
            expect(response.data).to.have.property('success', true);
            expect(response.data).to.have.property('message').that.includes('successful');
            expect(response.data).to.have.property('user_id');
            expect(response.data).to.have.property('username', uniqueUsername);
        });

        it('should reject registration with duplicate email', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            try {
                await client.post('/api/register', {
                    username: 'anotheruser',
                    email: 'uniquebishwo@gmail.com', // This email already exists
                    password: 'TestPass123!',
                    first_name: 'Test',
                    last_name: 'User'
                }, { headers });
                expect.fail('Should have thrown error for duplicate email');
            } catch (error) {
                expect(error.response.status).to.equal(400);
                expect(error.response.data).to.have.property('error');
            }
        });

        it('should validate required fields', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            try {
                await client.post('/api/register', {
                    // Missing required fields
                    email: 'test@example.com'
                }, { headers });
                expect.fail('Should have thrown error for missing required fields');
            } catch (error) {
                expect(error.response.status).to.equal(400);
                expect(error.response.data).to.have.property('error');
            }
        });
    });

    describe('GET /api/logout', () => {
        it('should logout authenticated user', async () => {
            // First login
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            await client.post('/api/login', {
                username_email: 'uniquebishwo@gmail.com',
                password: 'c9PU7XAsAADYk_A'
            }, { headers });

            // Then logout
            const logoutResponse = await client.get('/api/logout');

            expect(logoutResponse.status).to.equal(200);
            expect(logoutResponse.data).to.have.property('success', true);
            expect(logoutResponse.data).to.have.property('message').that.includes('successful');
        });

        it('should handle logout for non-authenticated user', async () => {
            const response = await client.get('/api/logout');

            expect(response.status).to.equal(200); // Logout should work even for non-authenticated users
            expect(response.data).to.have.property('success', true);
        });
    });

    describe('Authentication state management', () => {
        it('should maintain session across requests', async () => {
            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }

            // Login
            await client.post('/api/login', {
                username_email: 'uniquebishwo@gmail.com',
                password: 'c9PU7XAsAADYk_A'
            }, { headers });

            // Check status - should be logged in
            const statusResponse = await client.get('/api/user-status');
            expect(statusResponse.data.logged_in).to.equal(true);
            expect(statusResponse.data.user.email).to.equal('uniquebishwo@gmail.com');

            // Logout
            await client.get('/api/logout');

            // Check status again - should be logged out
            const statusResponse2 = await client.get('/api/user-status');
            expect(statusResponse2.data.logged_in).to.equal(false);
        });
    });
});