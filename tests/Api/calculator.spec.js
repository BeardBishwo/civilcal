const { test, expect } = require('@playwright/test');
const config = require('../config.json');
const users = config.environments[process.env.TEST_ENV || 'local'].users;
const apiEndpoints = config.apiEndpoints;
const civilConcreteVolumeFixture = require('../fixtures/calculators/civil_concrete_volume.json');

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

test.describe('Calculator API', () => {
  test('CALC-01: Execute concrete volume calculator with valid inputs', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({
        input_values: civilConcreteVolumeFixture.valid_inputs
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.result.volume).toBeGreaterThan(0);
    expect(body.result.unit).toBe('cubic_meters');
  });

  test('CALC-02: Execute calculator without authentication', async ({}) => {
    const response = await fetch(`${baseUrl}/api/calculator.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        input_values: civilConcreteVolumeFixture.valid_inputs
      })
    });
    expect(response.status).toBe(401);
    const body = await response.json();
    expect(body.error).toBe('Authentication required');
  });

  test('CALC-03: Execute calculator with invalid calculator type', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({
        input_values: { length: 5, width: 3, height: 2 }
      })
    });
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toBe('Invalid calculator type');
  });

  test('CALC-04: Execute calculator with missing input values', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({
        calculator_type: 'civil_concrete_volume'
        // Missing input_values
      })
    });
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toBe('Input values are required');
  });

  test('CALC-05: Execute calculator with invalid input values', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({
        input_values: civilConcreteVolumeFixture.invalid_inputs
      })
    });
    expect(response.status).toBe(400);
    const body = await response.json();
    expect(body.error).toMatch(/Invalid input values/i);
  });

  test('CALC-06: Execute calculator with edge case inputs', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({
        input_values: civilConcreteVolumeFixture.edge_cases
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.result.volume).toBe(0); // Zero dimensions should result in zero volume
  });

  test('CALC-07: Execute calculator using HTTP Basic Auth', async ({}) => {
    const credentials = Buffer.from(`${users.regular.username}:${users.regular.password}`).toString('base64');
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.execute}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Authorization': `Basic ${credentials}`
      },
      body: JSON.stringify({
        calculator_type: 'civil_concrete_volume',
        input_values: civilConcreteVolumeFixture.valid_inputs
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.result.volume).toBeGreaterThan(0);
  });

  test('CALC-08: Test alternate calculator endpoint (apiCalculate)', async ({}) => {
    const { cookies } = await loginAs(users.regular);
    const response = await fetch(`${baseUrl}${apiEndpoints.calculator.calculate}`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Cookie': cookies
      },
      body: JSON.stringify({
        input_values: civilConcreteVolumeFixture.valid_inputs
      })
    });
    expect(response.status).toBe(200);
    const body = await response.json();
    expect(body.success).toBe(true);
    expect(body.result.volume).toBeGreaterThan(0);
  });
});