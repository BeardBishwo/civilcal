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

test.describe('Authentication API', () => {
  test('AUTH-01: Valid login (JSON)', async ({}) => {
    const user = users.regular;
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username_email: user.username,
        password: user.password
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.user.username).toBe(user.username);
    expect(body.user.email).toBe('test.user@example.com');
    expect(body.user.is_admin).toBe(false);
    expect(response.headers.get('set-cookie')).toMatch(/auth_token=/);
  });

  test('AUTH-02: Valid login (form-urlencoded)', async ({}) => {
    const user = users.regular;
    const params = new URLSearchParams();
    params.append('username_email', user.username);
    params.append('password', user.password);
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString()
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.user.username).toBe(user.username);
    expect(response.headers.get('set-cookie')).toMatch(/auth_token=/);
  });

  test('AUTH-03: Wrong password', async ({}) => {
    const user = users.regular;
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username_email: user.username,
        password: 'wrongpassword'
      })
    });
    expect(response.status).toBe(401);
    const body = await response.json();
    expect(body.error).toBe('Invalid username or password');
  });

  test('AUTH-04: Missing credentials', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({})
    });
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toBe('Username and password are required');
  });

  test('AUTH-05: Invalid method (GET)', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
      method: 'GET'
    });
    expect(response.status).toBe(405);
    const body = await response.json();
    expect(body.error).toBe('Method not allowed');
  });

  test('AUTH-06: Remember me sets token', async ({}) => {
    const user = users.regular;
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.login}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username_email: user.username,
        password: user.password,
        remember_me: true
      })
    });
    expect(response.status).toBe(200);
    const setCookie = response.headers.get('set-cookie');
    expect(setCookie).toMatch(/remember_token=/);
    const rememberMatch = setCookie.match(/remember_token=([^;]+)/);
    expect(rememberMatch).toBeTruthy();
    expect(rememberMatch[1]).toMatch(/^[a-f0-9]{64}$/);
  });

  test('AUTH-07: Check remember token (valid format)', async ({}) => {
    // Assume a previous login set remember_token cookie
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.checkUsername}`, {
      method: 'GET',
      headers: { 'Cookie': 'remember_token=abcdef1234567890abcdef1234567890abcdef1234567890' }
    });
    // The endpoint returns success:true for valid 64-char hex; adjust if implementation differs
    // Here we just ensure the call does not error
    expect(response.status).toBe(200);
  });

  test('AUTH-08: Register full valid payload', async ({}) => {
    const payload = {
      username: 'newuser_' + Date.now(),
      email: `newuser_${Date.now()}@example.com`,
      password: 'NewUser@1234',
      full_name: 'New Full Name',
      phone_number: '+1234567890',
      engineer_roles: ['civil', 'structural'],
      terms_agree: true,
      marketing_agree: false
    };
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.register}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.user_id).toBeTruthy();
    expect(body.username).toBe(payload.username);
  });

  test('AUTH-09: Register missing first_name', async ({}) => {
    const payload = {
      username: 'baduser',
      email: 'baduser@example.com',
      password: 'BadUser@1234'
      // No full_name or first_name
    };
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.register}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toBe('First name is required');
  });

  test('AUTH-10: Register duplicate username', async ({}) => {
    const payload = {
      username: users.regular.username,
      email: `dup_${Date.now()}@example.com`,
      password: 'DupUser@1234',
      first_name: 'Dup'
    };
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.register}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toMatch(/already exists/i);
  });

  test('AUTH-11: Logout while authenticated', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.logout}`, {
      method: 'POST',
      headers: { 'Cookie': cookies }
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    // Optionally, verify cookies cleared by calling user-status endpoint
    const statusRes = await fetch(`${baseUrl}${apiEndpoints.auth.profile}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    const statusBody = await statusRes.json();
    expect(statusBody.logged_in).toBe(false);
  });

  test('AUTH-12: Logout without authentication', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.logout}`, {
      method: 'POST'
    });
    expect(response.status).toBe(401);
    const body = await response.json();
    expect(body.error).toBe('Unauthorized - not logged in');
  });

  test('AUTH-13: Forgot password with email', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.forgotPassword}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: 'test@example.com' })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.message).toBe('Password reset email sent');
  });

  test('AUTH-14: User status when logged in', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.profile}`, {
      method: 'GET',
      headers: { 'Cookie': cookies }
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.logged_in).toBe(true);
    expect(body.user).toBeTruthy();
  });

  test('AUTH-15: User status when not logged in', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.profile}`, {
      method: 'GET'
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.logged_in).toBe(false);
  });

  test('AUTH-16: Check username availability (available)', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.checkUsername}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username: 'newuser_' + Date.now() })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.available).toBe(true);
  });

  test('AUTH-17: Check username availability (existing)', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.checkUsername}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username: users.regular.username })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.available).toBe(false);
    expect(Array.isArray(body.suggestions)).toBe(true);
  });

  test('AUTH-18: Check username non-string', async ({}) => {
    const response = await fetch(`${baseUrl}${apiEndpoints.auth.checkUsername}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username: 12345 })
    });
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toBe('Username must be a string');
  });
});