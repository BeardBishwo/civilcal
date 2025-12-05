const { test, expect } = require('@playwright/test');
const config = require('../config.json');
const users = config.environments[process.env.TEST_ENV || 'local'].users;
const apiEndpoints = config.apiEndpoints;

const baseUrl = config.environments[process.env.TEST_ENV || 'local'].baseUrl;

// Helper to perform login and return cookies
async function loginAs(user) {
  const response = await fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      username_email: user.username,
      password: user.password
    })
  });
  if (!response.ok) throw new Error(`Login failed: ${response.status}`);
  const setCookie = response.headers.get('set-cookie');
  return { cookies: setCookie ? setCookie.split(';')[0] : '' };
}

test.describe('Admin Dashboard API', () => {
  test('ADMIN-DASH-01: Get dashboard data with HTTP Basic Auth', async ({}) => {
    const credentials = Buffer.from(`${users.admin.username}:${users.admin.password}`).toString('base64');
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.dashboard}`, {
      method: 'GET',
      headers: { 
        'Authorization': `Basic ${credentials}`
      }
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.stats).toBeTruthy();
    expect(typeof body.stats.total_users).toBe('number');
    expect(typeof body.stats.total_calculations).toBe('number');
    expect(typeof body.stats.active_users_today).toBe('number');
  });

  test('ADMIN-DASH-02: Get dashboard data with session auth', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.dashboard}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.stats).toBeTruthy();
    expect(typeof body.stats.total_users).toBe('number');
    expect(typeof body.stats.total_calculations).toBe('number');
    expect(typeof body.stats.active_users_today).toBe('number');
  });

  test('ADMIN-DASH-03: Access denied for non-admin user', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.dashboard}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    expect(response.status).toBe(403);
    const body = await response.json();
    expect(body.error).toBe('Admin access required');
  });

  test('ADMIN-DASH-04: Access denied without authentication', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.dashboard}`, {
      method: 'GET'
    });
    expect(response.status).toBe(401);
    const body = await response.json();
    expect(body.error).toBe('Authentication required');
  });

  test('ADMIN-DASH-05: Invalid HTTP method', async ({}) => {
    const credentials = Buffer.from(`${users.admin.username}:${users.admin.password}`).toString('base64');
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.dashboard}`, {
      method: 'POST',
      headers: { 
        'Authorization': `Basic ${credentials}`
      }
    });
    expect(response.status).toBe(405);
    const body = await response.json();
    expect(body.error).toBe('Method not allowed');
  });

  test('ADMIN-DASH-06: Dashboard data structure validation', async ({}) => {
    const credentials = Buffer.from(`${users.admin.username}:${users.admin.password}`).toString('base64');
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.dashboard}`, {
      method: 'GET',
      headers: { 
        'Authorization': `Basic ${credentials}`
      }
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    
    // Validate required stats fields
    const requiredFields = ['total_users', 'total_calculations', 'active_users_today', 'new_users_today', 'popular_calculators'];
    requiredFields.forEach(field => {
      expect(body.stats).toHaveProperty(field);
    });
    
    // Validate popular_calculators is an array
    expect(Array.isArray(body.stats.popular_calculators)).toBe(true);
    
    // Validate timestamp
    expect(typeof body.generated_at).toBe('string');
    expect(body.generated_at).toMatch(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/);
  });
});