#!/usr/bin/env node

const { spawn } = require('child_process');
const path = require('path');

// Get environment from command line args or default to local
const env = process.argv[2] || 'local';

// Map environment to config file
const configMap = {
  'local': 'local',
  'staging': 'staging', 
  'prod': 'production'
};

const configEnv = configMap[env] || 'local';

console.log(`🚀 Running API tests for environment: ${configEnv}`);

// Set environment variable for config
process.env.TEST_ENV = configEnv;

// Run Playwright tests
const playwrightProcess = spawn('npx', ['playwright', 'test', 'tests/Api/', '--reporter=html'], {
  stdio: 'inherit',
  shell: true,
  cwd: process.cwd()
});

playwrightProcess.on('close', (code) => {
  if (code === 0) {
    console.log('✅ All API tests passed successfully!');
    process.exit(0);
  } else {
    console.error(`❌ API tests failed with exit code: ${code}`);
    process.exit(code);
  }
});

playwrightProcess.on('error', (error) => {
  console.error('❌ Error running API tests:', error);
  process.exit(1);
});