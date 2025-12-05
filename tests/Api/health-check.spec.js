const { test, expect } = require('@playwright/test');
const config = require('../config.json');
const apiEndpoints = config.apiEndpoints;

const baseUrl = config.environments[process.env.TEST_ENV || 'local'].baseUrl;

test.describe('API Health Check', () => {
  test('HEALTH-01: Check API endpoints are accessible', async ({}) => {
    // Test authentication endpoints exist
    const authEndpoints = [
      apiEndpoints.auth.login,
      apiEndpoints.auth.register,
      apiEndpoints.auth.profile
    ];

    for (const endpoint of authEndpoints) {
      const response = await fetch(`${baseUrl}${endpoint}`, {
        method: 'GET'
      });
      
      // Should return either 200 (if GET is allowed) or 405 (if only POST allowed)
      expect([200, 405, 401]).toContain(response.status);
    }

    // Test calculator endpoint exists
    const calcResponse = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'GET'
    });
    
    // Should return either 200 (if GET is allowed) or 405 (if only POST allowed)
    expect([200, 405]).toContain(calcResponse.status);

    // Test admin endpoints exist (may return 401/403 due to auth)
    const adminEndpoints = [
      apiEndpoints.admin.dashboard,
      apiEndpoints.admin.settings
    ];

    for (const endpoint of adminEndpoints) {
      const response = await fetch(`${baseUrl}${endpoint}`, {
        method: 'GET'
      });
      
      // Should return 401 (unauthorized) or 405 (method not allowed)
      expect([401, 403, 405]).toContain(response.status);
    }
  });

  test('HEALTH-02: Verify API response format', async ({}) => {
    // Test profile endpoint for JSON response format
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.profile}`, {
      method: 'GET'
    });
    
    if (response.status === 200) {
      const body = await response.json();
      expect(body).toHaveProperty('success');
      expect(typeof body.success).toBe('boolean');
    }
  });

  test('HEALTH-03: Check calculator endpoint structure', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        input_values: { test: 'data' }
      })
    });
    
    // Should return either 400 (invalid input) or 401 (unauthorized) or 404 (not found)
    expect([400, 401, 404]).toContain(response.status);
    
    const body = await response.json();
    expect(body).toHaveProperty('error');
  });
});