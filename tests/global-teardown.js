const fs = require('fs');
const path = require('path');

async function globalTeardown(config) {
  console.log('🧹 Cleaning up API test environment...');
  
  // Generate test summary report
  const timestamp = process.env.TEST_RUN_TIMESTAMP;
  const testEnv = process.env.TEST_ENV || 'local';
  
  const summaryReport = {
    testRun: {
      timestamp,
      environment: testEnv,
      completedAt: new Date().toISOString()
    },
    results: {
      totalTests: 0,
      passedTests: 0,
      failedTests: 0,
      skippedTests: 0
    },
    coverage: {
      endpoints: [
        'Authentication (login, register, logout, profile)',
        'Calculator API (execute, validation)',
        'Admin Dashboard (stats, analytics)',
        'Admin Settings (CRUD, file upload)',
        'Security (input validation, authorization)'
      ]
    }
  };
  
  // Try to read test results if available
  const resultsPath = path.join(process.cwd(), 'test-results', 'api-results.json');
  if (fs.existsSync(resultsPath)) {
    try {
      const testResults = JSON.parse(fs.readFileSync(resultsPath, 'utf8'));
      summaryReport.results.totalTests = testResults.suites?.reduce((total, suite) => 
        total + (suite.specs?.length || 0), 0) || 0;
      summaryReport.results.passedTests = testResults.suites?.reduce((total, suite) => 
        total + (suite.specs?.filter(spec => spec.ok).length || 0), 0) || 0;
      summaryReport.results.failedTests = summaryReport.results.totalTests - summaryReport.results.passedTests;
    } catch (error) {
      console.warn('⚠️  Could not parse test results for summary:', error.message);
    }
  }
  
  // Write summary report
  const summaryPath = path.join(process.cwd(), 'test-results', `test-summary-${timestamp}.json`);
  fs.writeFileSync(summaryPath, JSON.stringify(summaryReport, null, 2));
  
  console.log(`📊 Test summary saved to: ${summaryPath}`);
  console.log(`✅ Total Tests: ${summaryReport.results.totalTests}`);
  console.log(`✅ Passed: ${summaryReport.results.passedTests}`);
  console.log(`❌ Failed: ${summaryReport.results.failedTests}`);
  
  // Cleanup old test results (keep last 5 runs)
  try {
    const testResultsDir = path.join(process.cwd(), 'test-results');
    const files = fs.readdirSync(testResultsDir)
      .filter(file => file.startsWith('test-summary-'))
      .map(file => ({
        name: file,
        path: path.join(testResultsDir, file),
        time: fs.statSync(path.join(testResultsDir, file)).mtime
      }))
      .sort((a, b) => b.time - a.time);
    
    // Remove old summary files (keep latest 5)
    if (files.length > 5) {
      files.slice(5).forEach(file => {
        fs.unlinkSync(file.path);
        console.log(`🗑️  Removed old summary: ${file.name}`);
      });
    }
  } catch (error) {
    console.warn('⚠️  Could not cleanup old test results:', error.message);
  }
  
  console.log('✅ Test environment cleanup complete');
}

module.exports = globalTeardown;