const fs = require('fs');
const path = require('path');

async function globalSetup(config) {
  console.log('🔧 Setting up API test environment...');
  
  // Ensure test results directory exists
  const testResultsDir = path.join(process.cwd(), 'test-results');
  if (!fs.existsSync(testResultsDir)) {
    fs.mkdirSync(testResultsDir, { recursive: true });
  }
  
  // Create timestamp for test run
  const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
  process.env.TEST_RUN_TIMESTAMP = timestamp;
  
  // Log test environment info
  const testEnv = process.env.TEST_ENV || 'local';
  console.log(`📋 Test Environment: ${testEnv}`);
  console.log(`📅 Test Run Timestamp: ${timestamp}`);
  
  // Validate test configuration
  const configPath = path.join(process.cwd(), 'tests', 'config.json');
  if (!fs.existsSync(configPath)) {
    throw new Error(`Configuration file not found: ${configPath}`);
  }
  
  const testConfig = JSON.parse(fs.readFileSync(configPath, 'utf8'));
  const baseUrl = testConfig.environments[testEnv].baseUrl;
  
  if (!baseUrl) {
    throw new Error('Base URL not configured in test configuration');
  }
  
  console.log(`🌐 Target Base URL: ${baseUrl}`);
  
  // Perform a basic health check on the target API
  try {
    const response = await fetch(`${baseUrl}/api/profile.php`, {
      method: 'GET',
      timeout: 5000
    });
    
    if (response.status === 404) {
      console.warn('⚠️  Warning: API endpoint not found - ensure the application is running');
    } else {
      console.log('✅ API health check passed');
    }
  } catch (error) {
    console.warn('⚠️  Warning: Could not perform API health check:', error.message);
  }
  
  console.log('🚀 Test environment setup complete');
}

module.exports = globalSetup;