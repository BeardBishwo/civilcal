const { test, expect } = require('@playwright/test');
const config = require('../config.json');
const users = config.environments[process.env.TEST_ENV || 'local'].users;
const apiEndpoints = config.apiEndpoints;
const defaultSettings = require('../fixtures/settings_default.json');

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

test.describe('Admin Settings API', () => {
  test('ADMIN-SET-01: Get current settings', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.settings).toBeTruthy();
    expect(typeof body.settings.site_name).toBe('string');
    expect(typeof body.settings.site_description).toBe('string');
  });

  test('ADMIN-SET-02: Update settings with valid data', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    const updateData = {
      site_name: 'Test Site Updated',
      site_description: 'Updated description for testing',
      contact_email: 'test@example.com',
      maintenance_mode: false
    };
    
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify(updateData)
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.message).toBe('Settings updated successfully');
  });

  test('ADMIN-SET-03: Update settings with invalid data', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    const invalidData = {
      site_name: '', // Empty site name should be invalid
      contact_email: 'invalid-email' // Invalid email format
    };
    
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify(invalidData)
    });
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toMatch(/Invalid input data/i);
  });

  test('ADMIN-SET-04: Access denied for non-admin user', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}/api/admin/settings.php`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    expect(response.status).toBe(403);
    const body = await response.json();
    expect(body.error).toBe('Admin access required');
  });

  test('ADMIN-SET-05: CSRF token required for POST', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({ site_name: 'Test' })
    });
    expect(response.status).toBe(403);
    const body = await response.json();
    expect(body.error).toBe('CSRF token required');
  });

  test('ADMIN-SET-06: Upload logo file', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    
    // First get CSRF token
    const getResponse = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    const getBody = await getResponse.json();
    const csrfToken = getBody.csrf_token;
    
    // Create a simple test file content (base64 encoded small PNG)
    const fileContent = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
    
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Cookie': cookies,
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        action: 'upload_logo',
        file: fileContent
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.message).toBe('Logo uploaded successfully');
  });

  test('ADMIN-SET-07: Upload favicon file', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    
    // First get CSRF token
    const getResponse = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    const getBody = await getResponse.json();
    const csrfToken = getBody.csrf_token;
    
    // Create a simple test file content (base64 encoded small ICO)
    const fileContent = 'AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAQAABILAAASCwAAAAAAAAAAAAD///8A////AP///wD///8A////AP///wD///8A////AP///wD///8A////AP///wD///8A////AP///wD///8A';
    
    const response = await fetch(`${baseUrl}/api/admin/settings.php`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Cookie': cookies,
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        action: 'upload_favicon',
        file: fileContent
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.message).toBe('Favicon uploaded successfully');
  });

  test('ADMIN-SET-08: Export settings', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({ action: 'export' })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.data).toBeTruthy();
    expect(typeof body.data).toBe('object');
  });

  test('ADMIN-SET-09: Import settings', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    
    // First get CSRF token
    const getResponse = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    const getBody = await getResponse.json();
    const csrfToken = getBody.csrf_token;
    
    const response = await fetch(`${baseUrl}/api/admin/settings.php`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies,
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        action: 'import',
        settings: defaultSettings
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.message).toBe('Settings imported successfully');
  });

  test('ADMIN-SET-10: Reset settings to defaults', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    
    // First get CSRF token
    const getResponse = await fetch(`${baseUrl}/api/admin/settings.php`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    const getBody = await getResponse.json();
    const csrfToken = getBody.csrf_token;
    
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies,
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({ action: 'reset' })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.message).toBe('Settings reset to defaults');
  });

  test('ADMIN-SET-11: Test email configuration', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    
    // First get CSRF token
    const getResponse = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    const getBody = await getResponse.json();
    const csrfToken = getBody.csrf_token;
    
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies,
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        action: 'test_email',
        test_email: 'test@example.com'
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.message).toBe('Test email sent successfully');
  });
});