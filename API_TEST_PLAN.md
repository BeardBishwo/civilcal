# Bishwo Calculator API Test Plan

## 1. Introduction

### 1.1 Purpose
This document outlines a comprehensive test plan for the Bishwo Calculator API, ensuring thorough coverage of all endpoints, edge cases, and integration scenarios while explicitly excluding the `C:\laragon\www\Bishwo_Calculator\testsprite_tests` directory.

### 1.2 Scope
The test plan covers:
- All API endpoints across authentication, user management, calculator functions, and admin operations
- Functional, performance, security, and error handling testing
- Integration scenarios and edge cases
- Test automation strategies and reporting mechanisms

### 1.3 Objectives
- Achieve 95%+ API test coverage
- Ensure robust error handling and validation
- Validate security measures and authentication flows
- Establish maintainable test automation framework
- Provide comprehensive reporting for continuous improvement

## 2. API Endpoints Overview

Based on the code analysis, the following API endpoints have been identified:

### 2.1 Authentication Endpoints
- `POST /api/login.php` - User login
- `POST /api/register.php` - User registration
- `POST /api/logout.php` - User logout
- `POST /api/forgot-password.php` - Password reset
- `GET /api/check-username.php` - Username availability check
- `GET /api/user-status.php` - Current user status
- `GET /api/check-remember-token.php` - Remember me token validation

### 2.2 User Profile Endpoints
- `GET /api/profile.php` - Get user profile
- `POST /api/profile.php` - Update user profile

### 2.3 Calculator Endpoints
- `GET /api/calculator/{module}/{function}` - Dynamic calculator execution

### 2.4 Admin Endpoints
- `GET /api/admin/dashboard.php` - Admin dashboard stats
- `GET /api/admin/modules.php` - Module management
- `POST /api/admin/modules.php` - Toggle module status
- `GET /api/admin/health.php` - System health check
- `POST /api/admin/backup.php` - Database backup
- `GET /api/admin/activity.php` - User activity logs

## 3. Test Environments

### 3.1 Environment Requirements
- **Development Environment**: Local setup with full debugging capabilities
- **Staging Environment**: Production-like environment for integration testing
- **Production Environment**: Limited testing with monitoring focus

### 3.2 Test Data Requirements
- User accounts with different roles (admin, engineer, regular user)
- Valid and invalid authentication credentials
- Sample calculator inputs and expected outputs
- Edge case data (empty values, boundary values, invalid formats)

### 3.3 Environment Setup
```bash
# Sample environment setup commands
composer install
php database/setup_db.php
php database/seed_test_data.php
```

## 4. Test Cases

### 4.1 Authentication Testing

#### 4.1.1 Login Endpoint (`POST /api/login.php`)
- **Happy Path**: Valid credentials return success with user data
- **Error Cases**: Invalid credentials, missing fields, rate limiting
- **Edge Cases**: Remember me functionality, session management
- **Security**: SQL injection attempts, brute force protection

#### 4.1.2 Registration Endpoint (`POST /api/register.php`)
- **Validation**: Username/email uniqueness, password strength
- **Data Integrity**: Required fields, proper data types
- **Error Handling**: Duplicate entries, invalid formats

### 4.2 Calculator Function Testing

#### 4.2.1 Dynamic Calculator Endpoint (`GET /api/calculator/{module}/{function}`)
- **Functional Testing**: Valid module/function combinations
- **Error Handling**: Invalid modules, missing parameters
- **Performance**: Response time under load
- **Data Validation**: Input sanitization, output formatting

### 4.3 Admin Functionality Testing

#### 4.3.1 Dashboard Stats (`GET /api/admin/dashboard.php`)
- **Authentication**: Admin role verification
- **Data Accuracy**: Statistical calculations
- **Performance**: Large dataset handling

#### 4.3.2 Module Management (`GET/POST /api/admin/modules.php`)
- **State Management**: Module activation/deactivation
- **Error Handling**: Invalid module names
- **Security**: Unauthorized access attempts

## 5. Test Automation Strategy

### 5.1 Automation Framework
- **Tool Selection**: PHPUnit for backend testing, Postman/Newman for API testing
- **Test Organization**: Modular test suites by functionality
- **CI/CD Integration**: Automated test execution in pipelines

### 5.2 Test Data Management
- **Data Factories**: Test data generation for different scenarios
- **Environment Isolation**: Separate test databases
- **Cleanup Procedures**: Post-test data removal

### 5.3 Test Execution
```bash
# Sample test execution commands
./vendor/bin/phpunit tests/Api/
newman run postman_collection.json --reporters cli,json
```

## 6. Security Testing

### 6.1 Authentication & Authorization
- Session management validation
- Role-based access control testing
- Token expiration and renewal

### 6.2 Input Validation
- SQL injection attempts
- XSS vulnerability testing
- Parameter tampering

### 6.3 Data Protection
- Sensitive data encryption
- Secure cookie handling
- Password hashing verification

## 7. Performance Testing

### 7.1 Load Testing Scenarios
- Concurrent user simulations
- Peak load handling
- Response time benchmarks

### 7.2 Stress Testing
- Resource utilization monitoring
- Failure recovery testing
- Memory leak detection

## 8. Error Handling & Edge Cases

### 8.1 Comprehensive Error Scenarios
- Invalid input formats
- Missing required parameters
- Boundary value testing
- Exception handling verification

### 8.2 Recovery Testing
- System failure simulation
- Data corruption scenarios
- Backup/restore validation

## 9. Integration Testing

### 9.1 End-to-End Scenarios
- User registration → login → calculator usage flow
- Admin module management → dashboard updates
- Cross-module data consistency

### 9.2 Third-Party Integrations
- Database connection reliability
- External API dependencies
- Payment gateway testing (if applicable)

## 10. Reporting & Monitoring

### 10.1 Test Reporting
- Automated test result generation
- Coverage analysis reports
- Trend analysis over time

### 10.2 Continuous Monitoring
- Performance metric tracking
- Error rate monitoring
- Availability testing

## 11. Maintenance & Scalability

### 11.1 Test Maintenance
- Regular test suite updates
- New endpoint coverage
- Deprecated endpoint removal

### 11.2 Scalability Testing
- Horizontal scaling validation
- Database sharding testing
- Caching strategy effectiveness

## 12. Exclusion of testsprite_tests Directory

All testing activities will explicitly exclude the `C:\laragon\www\Bishwo_Calculator\testsprite_tests` directory as per requirements. No test files, data, or configurations will be placed in or reference this directory.

## 13. Test Plan Implementation Timeline

| Phase | Duration | Deliverables |
|-------|----------|-------------|
| Test Environment Setup | 3 days | Configured test environments with sample data |
| Core API Testing | 5 days | Functional test cases for all endpoints |
| Security Testing | 3 days | Vulnerability assessment and penetration testing |
| Performance Testing | 4 days | Load and stress test results |
| Integration Testing | 3 days | End-to-end scenario validation |
| Reporting Setup | 2 days | Automated reporting framework |
| Documentation | 2 days | Complete test documentation and runbooks |

## 14. Risk Assessment & Mitigation

### 14.1 Identified Risks
- **Data Privacy**: Ensure no real user data in test environments
- **Performance Impact**: Load testing on production-like environments only
- **Test Data Leakage**: Proper cleanup procedures between test runs

### 14.2 Mitigation Strategies
- Use synthetic test data only
- Implement environment isolation
- Automated test data cleanup scripts
- Regular security audits of test infrastructure

## 15. Appendices

### 15.1 Test Data Templates
```json
{
  "valid_user": {
    "username": "test_user_123",
    "email": "test@example.com",
    "password": "SecurePassword123!",
    "first_name": "Test",
    "last_name": "User"
  },
  "admin_user": {
    "username": "admin_test",
    "email": "admin@test.com",
    "password": "AdminPassword123!",
    "role": "admin"
  }
}
```

### 15.2 Sample Test Case Format
```markdown
### Test Case: User Login with Valid Credentials
- **Endpoint**: POST /api/login.php
- **Input**: Valid username and password
- **Expected**: HTTP 200, success=true, user data in response
- **Actual**: [To be filled during execution]
- **Status**: [Pass/Fail]
- **Notes**: Verify session creation and cookie setting
```

## 16. Version Control & Change Management

All test artifacts will be version controlled with clear change history. Major changes to the test plan will follow formal review and approval processes.

---

**Document Status**: Draft
**Last Updated**: 2025-12-05
**Version**: 1.0
**Author**: Documentation Specialist
**Reviewers**: QA Team, Development Team