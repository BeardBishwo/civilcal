# Bishwo Calculator API Tests

Comprehensive API test suite for the Bishwo Calculator AEC SaaS platform, designed for shared-hosting cPanel deployment environments.

## 🚀 Quick Start

### Prerequisites

- Node.js 16+ 
- npm or yarn
- Access to the Bishwo Calculator API endpoints

### Installation

```bash
# Install dependencies
npm install

# Install Playwright browsers
npx playwright install
```

### Running Tests

```bash
# Run tests against local environment
npm run test:local

# Run tests against staging environment
npm run test:staging

# Run tests against production environment
npm run test:prod

# Generate HTML report
npm run test:report
```

## 📁 Test Structure

```
tests/
├── Api/                          # API test suites
│   ├── auth.spec.js             # Authentication tests (18 tests)
│   ├── calculator.spec.js       # Calculator API tests (8 tests)
│   ├── admin-dashboard.spec.js  # Admin dashboard tests (6 tests)
│   ├── admin-settings.spec.js   # Admin settings tests (11 tests)
│   └── security.spec.js         # Security tests (10 tests)
├── fixtures/                     # Test data fixtures
│   ├── calculators/              # Calculator test data
│   │   └── civil_concrete_volume.json
│   └── settings_default.json     # Default settings
├── config.json                  # Environment configuration
├── global-setup.js              # Test environment setup
├── global-teardown.js           # Test environment cleanup
└── index.js                     # Test runner entry point
```

## 🔧 Configuration

### Environment Configuration

Edit `tests/config.json` to configure your test environments:

```json
{
  "baseUrl": "http://localhost/bishwo-calculator",
  "staging": {
    "baseUrl": "https://staging.bishwocalculator.com"
  },
  "production": {
    "baseUrl": "https://bishwocalculator.com"
  },
  "users": {
    "regular": {
      "username": "testuser",
      "password": "TestUser@1234",
      "email": "testuser@example.com"
    },
    "admin": {
      "username": "admin",
      "password": "Admin@1234",
      "email": "admin@example.com"
    }
  }
}
```

### Test Users

Ensure these users exist in your test environment:

- **Regular User**: `testuser` with password `TestUser@1234`
- **Admin User**: `admin` with password `Admin@1234`

## 📊 Test Coverage

### Authentication API (18 tests)
- ✅ Login with JSON and form data
- ✅ Registration validation
- ✅ Logout functionality
- ✅ User status checking
- ✅ Username availability
- ✅ Remember me functionality
- ✅ Password reset flow

### Calculator API (8 tests)
- ✅ Valid calculator execution
- ✅ Authentication requirements
- ✅ Input validation
- ✅ Invalid calculator types
- ✅ Edge case handling
- ✅ HTTP Basic Auth support

### Admin Dashboard API (6 tests)
- ✅ Dashboard statistics
- ✅ Authentication methods (Basic + Session)
- ✅ Authorization enforcement
- ✅ Data structure validation

### Admin Settings API (11 tests)
- ✅ Settings CRUD operations
- ✅ File upload (logo/favicon)
- ✅ Import/Export functionality
- ✅ CSRF protection
- ✅ Email configuration testing

### Security API (10 tests)
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Authorization bypass attempts
- ✅ CSRF token validation
- ✅ Input sanitization
- ✅ Rate limiting
- ✅ File upload security
- ✅ HTTP method enforcement

## 🎯 Test Categories

### Functional Tests
- Endpoint functionality validation
- Request/response verification
- Business logic testing
- Error handling validation

### Security Tests
- Input validation and sanitization
- Authentication and authorization
- Common vulnerability prevention
- Session management

### Integration Tests
- Cross-component workflows
- Data flow validation
- End-to-end scenarios

### Performance Tests
- Response time validation
- Load testing capabilities
- Resource usage monitoring

## 📈 Reporting

### HTML Report
Interactive HTML report with detailed test results:
```bash
npm run test:report
# View at: test-results/api-tests/index.html
```

### JSON Report
Machine-readable results for CI/CD integration:
```bash
# Generated at: test-results/api-results.json
```

### JUnit Report
For integration with test management systems:
```bash
# Generated at: test-results/api-results.xml
```

### Summary Report
Executive overview with key metrics:
```bash
# Generated at: test-results/test-summary-[timestamp].json
```

## 🔒 Security Considerations

### Test Data Security
- Use anonymized test data
- Never use production credentials
- Sanitize test data after runs
- Separate test database when possible

### API Security Testing
- SQL injection prevention
- XSS protection validation
- CSRF token verification
- Authorization enforcement
- Rate limiting validation

## 🚀 CI/CD Integration

### GitHub Actions Example

```yaml
name: API Tests
on: [push, pull_request]
jobs:
  api-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
        with:
          node-version: '18'
      - run: npm install
      - run: npx playwright install
      - run: npm run test:staging
      - uses: actions/upload-artifact@v3
        if: always()
        with:
          name: test-results
          path: test-results/
```

### GitLab CI Example

```yaml
api_tests:
  stage: test
  image: node:18
  script:
    - npm install
    - npx playwright install
    - npm run test:staging
  artifacts:
    when: always
    paths:
      - test-results/
    reports:
      junit: test-results/api-results.xml
```

## 🛠️ Maintenance

### Adding New Tests

1. Create test file in `tests/Api/`
2. Follow existing naming convention (`*.spec.js`)
3. Use existing helper functions for authentication
4. Update test documentation

### Updating Test Data

1. Modify fixtures in `tests/fixtures/`
2. Update configuration in `tests/config.json`
3. Test with all environments
4. Update documentation

### Performance Baselines

1. Update performance targets in `playwright.config.js`
2. Monitor trends over time
3. Adjust based on infrastructure changes
4. Document performance expectations

## 🐛 Troubleshooting

### Common Issues

**Tests fail with connection errors**
- Verify API endpoints are accessible
- Check network connectivity
- Validate configuration URLs

**Authentication tests fail**
- Verify test users exist
- Check user credentials
- Ensure proper permissions

**Performance tests fail**
- Check server load
- Verify baseline expectations
- Monitor resource usage

### Debug Mode

Run tests with additional debugging:

```bash
# Run with verbose output
DEBUG=* npm run test:local

# Run specific test file
npx playwright test tests/Api/auth.spec.js

# Run with trace on all tests
npx playwright test --trace on
```

## 📞 Support

For questions or issues with the API test suite:

1. Check this documentation
2. Review test logs and reports
3. Verify configuration settings
4. Check API endpoint availability

## 📝 License

This test suite is part of the Bishwo Calculator platform and follows the same licensing terms.

---

**Note**: This test suite is designed specifically for shared-hosting cPanel environments and may require adjustments for different deployment scenarios.