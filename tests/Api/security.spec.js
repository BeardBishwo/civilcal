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

test.describe('Security API Tests', () => {
  test('SEC-01: SQL Injection attempt in login', async ({}) => {
    const maliciousPayload = {
      username_email: "admin' OR '1'='1",
      password: "anything"
    };
    
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(maliciousPayload)
    });
    
    // Should return 401, not 500 (which would indicate SQL error)
    expect(response.status).toBe(401);
    const body = await response.json();
    expect(body.error).toBe('Invalid username or password');
  });

  test('SEC-02: XSS attempt in registration', async ({}) => {
    const maliciousPayload = {
      username: 'testuser_' + Date.now(),
      email: `testuser_${Date.now()}@example.com`,
      password: 'TestUser@1234',
      full_name: '<script>alert("XSS")</script>',
      phone_number: '+1234567890',
      engineer_roles: ['civil'],
      terms_agree: true,
      marketing_agree: false
    };
    
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.register}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(maliciousPayload)
    });
    
    // Should either reject the input or sanitize it
    if (response.status === 400) {
      const body = await response.json();
      expect(body.error).toMatch(/invalid/i);
    } else if (response.status === 200) {
      const body = await response.json();
      expect(body.success).toBe(true);
      // If accepted, the script should be sanitized in the database
    }
  });

  test('SEC-03: Authorization bypass attempt', async ({}) => {
    // Try to access admin endpoint with regular user credentials
    const { cookies } = await loginAs(users.regular);
    
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.dashboard}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    
    expect(response.status).toBe(403);
    const body = await response.json();
    expect(body.error).toBe('Admin access required');
  });

  test('SEC-04: CSRF token validation', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    
    // Try to update settings without CSRF token
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({
        site_name: 'Hacked Site'
      })
    });
    
    expect(response.status).toBe(403);
    const body = await response.json();
    expect(body.error).toBe('CSRF token required');
  });

  test('SEC-05: Input validation for calculator', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    
    // Try to inject malicious code in calculator inputs
    const maliciousInput = {
      calculator_type: 'civil_concrete_volume',
      input_values: {
        length: '<script>alert("XSS")</script>',
        width: '5; DROP TABLE users; --',
        height: 'union select * from users'
      }
    };
    
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify(maliciousInput)
    });
    
    // Should reject malicious input
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toMatch(/invalid/i);
  });

  test('SEC-06: Session fixation prevention', async ({}) => {
    // Login and get session
    const { cookies } = await loginAs(users.regular);
    
    // Try to use the same session after logout
    const logoutResponse = await fetch(`${baseUrl}/api/logout.php`, {
      method: 'POST',
      headers: { 'Cookie': cookies }
    });
    
    expect(logoutResponse.status).toBe(200);
    
    // Try to access protected resource with old session
    const profileResponse = await fetch(`${baseUrl}${apiEndpoints.auth.profile}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    
    expect(profileResponse.status).toBe(200);
    const profileBody = await profileResponse.json();
    expect(profileBody.logged_in).toBe(false);
  });

  test('SEC-07: Information disclosure prevention', async ({}) => {
    // Try to access non-existent endpoints
    const response = await fetch(`${baseUrl}/api/nonexistent.php`, {
      method: 'GET'
    });
    
    expect(response.status).toBe(404);
    
    // Check that error doesn't expose sensitive information
    const body = await response.json();
    expect(body.error).toBe('Endpoint not found');
    expect(body).not.toHaveProperty('stack_trace');
    expect(body).not.toHaveProperty('file_path');
  });

  test('SEC-08: Rate limiting on login attempts', async ({}) => {
    const invalidCredentials = {
      username_email: users.regular.username,
      password: 'wrongpassword'
    };
    
    // Make multiple failed login attempts rapidly
    const promises = Array(10).fill().map(() =>
      fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(invalidCredentials)
      })
    );
    
    const responses = await Promise.all(promises);
    
    // At least one should be rate limited after multiple attempts
    const rateLimitedResponses = responses.filter(r => r.status === 429);
    expect(rateLimitedResponses.length).toBeGreaterThan(0);
    
    if (rateLimitedResponses.length > 0) {
      const rateLimitedBody = await rateLimitedResponses[0].json();
      expect(rateLimitedBody.error).toMatch(/too many attempts/i);
    }
  });

  test('SEC-09: File upload validation', async ({}) => {
    const { cookies } = await loginAs(users.admin);
    
    // Try to upload a malicious file (PHP file)
    const maliciousFile = 'PD9waHAgcGhwaW5mbygpOyA/Pg=='; // Base64 encoded <?php phpinfo(); ?>
    
    const response = await fetch(`${baseUrl}${apiEndpoints.admin.settings}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'multipart/form-data',
        'Cookie': cookies
      },
      body: JSON.stringify({
        action: 'upload_logo',
        file: maliciousFile,
        filename: 'malicious.php'
      })
    });
    
    // Should reject PHP file uploads
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toMatch(/invalid file type/i);
  });

  test('SEC-10: HTTP method enforcement', async ({}) => {
    // Test that sensitive endpoints only accept appropriate methods
    const endpoints = [
      { path: '/api/login.php', allowed: ['POST'] },
      { path: '/api/register.php', allowed: ['POST'] },
      { path: '/api/logout.php', allowed: ['POST'] },
      { path: '/api/calculator.php', allowed: ['POST'] }
    ];
    
    for (const endpoint of endpoints) {
      // Try GET method
      const getResponse = await fetch(`${baseUrl}${endpoint.path}`, {
        method: 'GET'
      });
      
      if (!endpoint.allowed.includes('GET')) {
        expect(getResponse.status).toBe(405);
        const getBody = await getResponse.json();
        expect(getBody.error).toBe('Method not allowed');
      }
      
      // Try DELETE method
      const deleteResponse = await fetch(`${baseUrl}${endpoint.path}`, {
        method: 'DELETE'
      });
      
      if (!endpoint.allowed.includes('DELETE')) {
        expect(deleteResponse.status).toBe(405);
        const deleteBody = await deleteResponse.json();
        expect(deleteBody.error).toBe('Method not allowed');
      }
    }
  });
});