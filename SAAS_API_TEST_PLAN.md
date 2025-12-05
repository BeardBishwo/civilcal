# Bishwo Calculator - Comprehensive API Test Plan

## Executive Summary

This document outlines a comprehensive API testing strategy for the Bishwo Calculator AEC (Architecture, Engineering, Construction) SaaS platform. The test plan ensures thorough coverage of all API endpoints, edge cases, and integration scenarios while maintaining compatibility with shared-hosting cPanel deployment environments.

### Key Objectives
- Ensure API reliability and functionality across all user workflows
- Validate security measures and prevent common vulnerabilities
- Maintain performance standards suitable for shared hosting environments
- Provide automated testing for continuous integration and deployment
- Support scalability for the 2026 growth target

## 1. Test Scope & Coverage

### 1.1 API Domains

| Domain | Endpoints | Priority | Test Coverage |
|--------|-----------|----------|---------------|
| Authentication | login, register, logout, profile, forgot-password, check-username | Critical | 100% |
| Calculator Engine | calculator execution, validation, results | Critical | 100% |
| Admin Dashboard | stats, analytics, user management | High | 95% |
| Admin Settings | CRUD operations, file upload, configuration | High | 95% |
| Security | Input validation, authorization, rate limiting | Critical | 100% |

### 1.2 Test Types

| Test Type | Description | Frequency |
|-----------|-------------|-----------|
| Functional Testing | Endpoint functionality, request/response validation | Every build |
| Integration Testing | Cross-component workflows, data flow | Every build |
| Security Testing | Vulnerability scanning, input validation | Weekly |
| Performance Testing | Response times, load handling | Bi-weekly |
| Reliability Testing | Error handling, recovery | Weekly |

## 2. Test Environment Configuration

### 2.1 Environment Setup

| Environment | Purpose | URL | Database |
|-------------|---------|-----|----------|
| Local | Development & debugging | http://localhost/bishwo-calculator | SQLite/MySQL |
| Staging | Pre-production validation | https://staging.bishwocalculator.com | MySQL |
| Production | Live environment monitoring | https://bishwocalculator.com | MySQL |

### 2.2 Shared Hosting Constraints

- **Memory Limits**: Tests designed to work within 256MB PHP memory limits
- **Execution Time**: API calls timeout after 30 seconds
- **Concurrent Connections**: Maximum 20 simultaneous API requests
- **File Upload**: Limited to 10MB per file, validated extensions only

### 2.3 Test Data Management

#### 2.3.1 Test Users
```json
{
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
```

#### 2.3.2 Calculator Test Data
- **Civil Engineering**: Concrete volume, structural load calculations
- **Electrical**: Power consumption, voltage drop calculations
- **HVAC**: Heat load, duct sizing calculations
- **Plumbing**: Pipe flow, pressure calculations

#### 2.3.3 Data Isolation
- Each test run uses unique identifiers (timestamps)
- Test data cleanup after each test suite
- Separate test database schema when possible

## 3. Detailed Test Cases

### 3.1 Authentication API Tests

#### 3.1.1 Login Endpoint (`/api/login.php`)

| Test ID | Description | Input | Expected Output |
|---------|-------------|-------|-----------------|
| AUTH-01 | Valid login (JSON) | Valid credentials | 200, user data, auth_token |
| AUTH-02 | Valid login (form-urlencoded) | Valid credentials | 200, user data, auth_token |
| AUTH-03 | Wrong password | Valid username, wrong password | 401, error message |
| AUTH-04 | Missing credentials | Empty request body | 400, validation error |
| AUTH-05 | Invalid method | GET request | 405, method error |
| AUTH-06 | Remember me | Valid credentials + remember_me | 200, remember_token set |

#### 3.1.2 Registration Endpoint (`/api/register.php`)

| Test ID | Description | Input | Expected Output |
|---------|-------------|-------|-----------------|
| AUTH-08 | Full valid payload | Complete user data | 200, user_id, username |
| AUTH-09 | Missing first_name | Incomplete user data | 400, validation error |
| AUTH-10 | Duplicate username | Existing username | 400, duplicate error |

### 3.2 Calculator API Tests

#### 3.2.1 Calculator Execution (`/api/calculator.php`)

| Test ID | Description | Input | Expected Output |
|---------|-------------|-------|-----------------|
| CALC-01 | Valid concrete calculation | Valid dimensions | 200, volume result |
| CALC-02 | Unauthenticated access | No auth token | 401, auth required |
| CALC-03 | Invalid calculator type | Unknown calculator | 400, invalid type |
| CALC-04 | Missing input values | No input_values | 400, validation error |
| CALC-05 | Invalid input values | Negative dimensions | 400, validation error |

### 3.3 Admin Dashboard API Tests

#### 3.3.1 Dashboard Stats (`/api/admin/dashboard.php`)

| Test ID | Description | Input | Expected Output |
|---------|-------------|-------|-----------------|
| ADMIN-DASH-01 | HTTP Basic Auth | Admin credentials | 200, dashboard stats |
| ADMIN-DASH-02 | Session Auth | Admin session | 200, dashboard stats |
| ADMIN-DASH-03 | Non-admin access | Regular user | 403, access denied |
| ADMIN-DASH-04 | No authentication | No auth | 401, auth required |

### 3.4 Security Tests

#### 3.4.1 Input Validation

| Test ID | Description | Input | Expected Output |
|---------|-------------|-------|-----------------|
| SEC-01 | SQL Injection | Malicious SQL in login | 401, not 500 |
| SEC-02 | XSS Attempt | Script tags in registration | 400 or sanitized |
| SEC-03 | Authorization Bypass | Regular user to admin | 403, access denied |
| SEC-04 | CSRF Validation | POST without token | 403, CSRF required |

## 4. Test Automation Strategy

### 4.1 Technology Stack

- **Test Framework**: Playwright (Node.js)
- **Assertion Library**: Built-in Playwright expect
- **Reporting**: HTML, JSON, JUnit formats
- **CI/CD Integration**: GitHub Actions, GitLab CI

### 4.2 Test Structure

```
tests/
├── Api/
│   ├── auth.spec.js          # Authentication tests
│   ├── calculator.spec.js    # Calculator API tests
│   ├── admin-dashboard.spec.js # Admin dashboard tests
│   ├── admin-settings.spec.js  # Admin settings tests
│   └── security.spec.js      # Security tests
├── fixtures/
│   ├── calculators/          # Calculator test data
│   └── settings_default.json # Default settings
├── config.json              # Environment configuration
├── global-setup.js          # Test environment setup
├── global-teardown.js       # Test environment cleanup
└── index.js                 # Test runner entry point
```

### 4.3 Execution Commands

```bash
# Run tests locally
npm run test:local

# Run tests on staging
npm run test:staging

# Run tests on production
npm run test:prod

# Generate HTML report
npm run test:report
```

### 4.4 CI/CD Integration

#### 4.4.1 GitHub Actions Workflow
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
      - run: npm run test:staging
      - uses: actions/upload-artifact@v3
        if: always()
        with:
          name: test-results
          path: test-results/
```

## 5. Performance Testing

### 5.1 Performance Benchmarks

| Endpoint | Target Response Time | P95 Target | Concurrent Users |
|----------|---------------------|------------|------------------|
| Login | < 500ms | < 800ms | 10 |
| Calculator Execution | < 300ms | < 600ms | 5 |
| Admin Dashboard | < 800ms | < 1200ms | 3 |
| Settings CRUD | < 400ms | < 700ms | 5 |

### 5.2 Load Testing Strategy

- **Baseline**: Single user performance
- **Moderate Load**: 5 concurrent users
- **Peak Load**: 10 concurrent users (shared hosting limit)
- **Stress Test**: 15 concurrent users (to find breaking point)

### 5.3 Monitoring Metrics

- Response time percentiles (50th, 95th, 99th)
- Error rates by endpoint
- Memory usage during test execution
- Database query performance

## 6. Security Testing

### 6.1 Security Test Categories

| Category | Tests | Frequency |
|----------|-------|-----------|
| Authentication | Session management, password policies | Every build |
| Authorization | Role-based access control | Every build |
| Input Validation | SQL injection, XSS, CSRF | Every build |
| Data Protection | Sensitive data exposure | Weekly |
| Rate Limiting | Brute force prevention | Weekly |

### 6.2 Security Test Scenarios

1. **Authentication Bypass**: Attempt to access protected resources without proper authentication
2. **Privilege Escalation**: Regular user attempting admin operations
3. **Data Injection**: SQL injection, XSS, command injection attempts
4. **Session Security**: Session fixation, hijacking, timeout validation
5. **File Upload**: Malicious file upload prevention

## 7. Reporting & Monitoring

### 7.1 Test Reports

#### 7.1.1 Automated Reports
- **HTML Report**: Interactive dashboard with test results
- **JSON Report**: Machine-readable results for CI/CD
- **JUnit Report**: Integration with test management systems
- **Summary Report**: Executive overview with key metrics

#### 7.1.2 Report Contents
- Test execution summary (passed/failed/skipped)
- Performance metrics by endpoint
- Security test results
- Error logs and stack traces
- Historical trend analysis

### 7.2 Monitoring Dashboard

#### 7.2.1 Real-time Metrics
- API response times
- Error rates by endpoint
- Active user sessions
- System resource usage

#### 7.2.2 Alerts
- Response time degradation (> 2x baseline)
- Error rate increase (> 5%)
- Security test failures
- Performance regression

## 8. Maintenance & Updates

### 8.1 Test Maintenance Schedule

| Frequency | Task | Owner |
|-----------|------|-------|
| Daily | Review test failures | QA Team |
| Weekly | Update test data | QA Team |
| Monthly | Performance baseline review | DevOps |
| Quarterly | Security test update | Security Team |
| Semi-annually | Full test suite review | QA Lead |

### 8.2 Test Update Process

1. **New Endpoint Addition**
   - Create test specification
   - Implement test cases
   - Update fixtures and configuration
   - Add to CI/CD pipeline

2. **API Changes**
   - Review impact on existing tests
   - Update test assertions
   - Validate backward compatibility
   - Update documentation

3. **Test Failure Resolution**
   - Analyze root cause
   - Fix test or API issue
   - Update test data if needed
   - Verify resolution

## 9. Risk Assessment

### 9.1 High-Risk Areas

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| API Breaking Changes | High | Medium | Version control, backward compatibility |
| Performance Degradation | High | Low | Continuous monitoring, alerts |
| Security Vulnerabilities | Critical | Low | Regular security scans, updates |
| Test Environment Failure | Medium | Low | Redundant environments, health checks |

### 9.2 Contingency Plans

- **API Unavailability**: Skip tests, mark as inconclusive
- **Performance Issues**: Run reduced test suite, investigate separately
- **Security Test Failures**: Block deployment until resolved
- **Test Data Corruption**: Refresh test database, rerun tests

## 10. Success Metrics

### 10.1 Quality Metrics

| Metric | Target | Current | Trend |
|--------|--------|---------|-------|
| Test Coverage | > 95% | TBD | ↗️ |
| Pass Rate | > 98% | TBD | ↗️ |
| Performance Compliance | 100% | TBD | ↗️ |
| Security Compliance | 100% | TBD | ↗️ |

### 10.2 Business Impact Metrics

- **API Reliability**: Uptime percentage
- **User Experience**: Response time satisfaction
- **Development Velocity**: Time to deploy new features
- **Risk Reduction**: Security incidents prevented

## 11. Implementation Timeline

### Phase 1: Foundation (Week 1-2)
- [x] Set up test framework and configuration
- [x] Create basic authentication tests
- [x] Implement calculator API tests
- [x] Set up CI/CD integration

### Phase 2: Expansion (Week 3-4)
- [x] Add admin dashboard tests
- [x] Implement security test suite
- [x] Create performance benchmarks
- [x] Set up monitoring and reporting

### Phase 3: Optimization (Week 5-6)
- [ ] Fine-tune performance tests
- [ ] Optimize test execution time
- [ ] Implement advanced security scenarios
- [ ] Create comprehensive documentation

### Phase 4: Maintenance (Ongoing)
- [ ] Regular test updates
- [ ] Performance monitoring
- [ ] Security scan integration
- [ ] Continuous improvement

## 12. Conclusion

This comprehensive API test plan provides a robust foundation for ensuring the quality, security, and performance of the Bishwo Calculator platform. The automated testing strategy aligns with shared-hosting constraints while supporting the scalability goals for 2026.

Regular execution of these tests will:
- Maintain high API reliability and user satisfaction
- Prevent security vulnerabilities and data breaches
- Ensure performance standards are met
- Support rapid development and deployment cycles
- Provide confidence in platform stability for growth

The test suite is designed to evolve with the platform, ensuring continued quality assurance as new features and endpoints are added.