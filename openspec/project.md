# Project Context

## Purpose
Bishwo Calculator is a comprehensive AEC (Architecture, Engineering, Construction) SaaS platform providing specialized calculators for electrical engineers, contractors, and construction professionals. The platform offers modular calculation tools for various engineering disciplines including civil, electrical, plumbing, HVAC, fire protection, structural engineering, and project management. Key goals include:

- Provide accurate, industry-standard calculations following NEC, IEC, IEEE standards
- Support both free and premium features with subscription-based monetization
- Enable calculation history tracking and user account management
- Offer responsive design for mobile field use
- Support multi-currency and international standards
- Provide comprehensive admin dashboard for system management

## Tech Stack

### Backend
- **PHP 7.4+** - Primary backend language with PSR-12 coding standards
- **MySQL** - Primary database with UTF8MB4 charset
- **Composer** - PHP dependency management
- **FastRoute** - Lightweight routing framework
- **PHPMailer** - Email functionality
- **Monolog** - Logging system
- **PHPFastCache** - Caching layer
- **Respect\Validation** - Input validation
- **Carbon** - Date/time handling
- **Guzzle** - HTTP client for external APIs

### Frontend
- **HTML5/CSS3** - Modern web standards
- **JavaScript ES6+** - Client-side functionality
- **Bootstrap 4/5** - Responsive UI framework
- **jQuery** - DOM manipulation and AJAX
- **Chart.js** - Data visualization
- **Font Awesome** - Icon library

### Development & Testing
- **Node.js/npm** - Frontend build tools and testing
- **Playwright** - API and E2E testing framework
- **Git** - Version control
- **Laravel Mix** - Asset compilation (if applicable)

### External Services
- **PayPal** - Payment processing and subscriptions
- **Stripe** - Alternative payment gateway
- **Mollie** - European payment processing
- **Google APIs** - OAuth authentication, reCAPTCHA
- **MaxMind** - Geolocation services
- **Sentry** - Error tracking and monitoring

## Project Conventions

### Code Style
- **PHP**: Follow PSR-12 coding standards with consistent indentation and naming
- **JavaScript**: Use ES6+ features, consistent formatting, and meaningful variable names
- **CSS**: Implement BEM methodology for CSS class naming
- **File Naming**: Use snake_case for PHP files, kebab-case for CSS/JS assets
- **Namespace Structure**: PSR-4 autoloading with `App\` namespace prefix
- **Documentation**: Inline PHPDoc comments for all public methods and classes

### Architecture Patterns
- **Modular Plugin System**: Each calculator implemented as independent plugin module
- **Service Layer**: Business logic encapsulated in service classes (e.g., `CalculationService`)
- **Repository Pattern**: Data access abstraction through repository classes
- **Factory Pattern**: Calculator instantiation through `CalculatorFactory`
- **MVC Architecture**: Clear separation between models, views, and controllers
- **Dependency Injection**: Services injected where needed for testability
- **Middleware Pattern**: Request processing through middleware stack (auth, CSRF, etc.)

### Testing Strategy
- **Unit Tests**: All core functionality requires unit tests using PHPUnit
- **Integration Tests**: Calculator workflows and API endpoints tested
- **API Testing**: Comprehensive API test suite using Playwright
- **User Acceptance**: Calculations validated against industry-standard references
- **Performance Testing**: Response time targets: API <200ms, calculations <1s
- **Automated Testing**: CI/CD pipeline with automated test execution

### Git Workflow
- **Main Branch**: `main` branch for production-ready code
- **Feature Branches**: `feature/description` for new features
- **Bug Fixes**: `hotfix/issue-description` for critical fixes
- **Release Branches**: `release/vX.Y.Z` for version preparation
- **Commit Messages**: Descriptive messages following conventional commits
- **Pull Requests**: All changes require peer review before merging
- **Semantic Versioning**: Follow semantic versioning for releases

## Domain Context

### AEC Industry Standards
- **Electrical Codes**: NEC (National Electrical Code), IEC standards
- **Building Codes**: Local and international building regulations
- **Safety Standards**: OSHA, electrical safety guidelines
- **Engineering Calculations**: Industry-standard formulas and methodologies
- **Unit Systems**: Support for both metric and imperial units

### Calculator Categories
- **Civil Engineering**: Concrete, structural, earthwork calculations
- **Electrical Engineering**: Load calculations, wire sizing, voltage drop
- **MEP Systems**: Mechanical, Electrical, Plumbing calculations
- **Project Management**: Estimation, scheduling, cost analysis
- **Site Engineering**: Surveying, land measurement tools

### User Types
- **Free Users**: Basic calculator access with limited features
- **Premium Users**: Advanced features, calculation history, exports
- **Administrators**: System management, user management, analytics
- **Guest Users**: Limited calculator access without account

## Important Constraints

### Technical Constraints
- **PHP Version**: Minimum PHP 7.4, targeting compatibility with PHP 8.x
- **Database**: MySQL 5.7+ with specific charset requirements (UTF8MB4)
- **Memory Limits**: Calculations optimized for efficient memory usage
- **Performance**: System designed for 1000+ concurrent users
- **Mobile Support**: Responsive design required for field use on mobile devices

### Business Constraints
- **Monetization**: Subscription-based model with free tier limitations
- **Compliance**: GDPR compliance for European users
- **Data Protection**: Secure handling of user calculation data
- **Payment Processing**: PCI compliance for payment handling
- **Intellectual Property**: Respect for third-party licenses and patents

### Regulatory Constraints
- **Accessibility**: WCAG 2.1 AA compliance for web interfaces
- **Data Protection**: GDPR, local data protection laws
- **Financial Regulations**: Payment processing compliance (PCI DSS)
- **Industry Standards**: Adherence to electrical and building codes

## External Dependencies

### Payment Gateways
- **PayPal REST API** - Primary payment processing
- **Stripe API** - Alternative payment processing
- **Mollie API** - European payment processing
- **Currency Conversion** - Real-time exchange rate services

### Authentication & Security
- **Google OAuth 2.0** - Social login integration
- **reCAPTCHA v3** - Bot protection and spam prevention
- **MaxMind GeoIP** - Geolocation and fraud detection
- **Sentry** - Error tracking and performance monitoring

### Communication Services
- **SMTP Services** - Email delivery (SendGrid, Mailgun, or local SMTP)
- **SMS Gateways** - Optional SMS notifications
- **Push Notifications** - Web push notification services

### Development Tools
- **GitHub/GitLab** - Source code hosting and CI/CD
- **Composer** - PHP package management
- **NPM** - JavaScript package management
- **Docker** - Containerization for development and deployment

### Monitoring & Analytics
- **Google Analytics** - User behavior tracking
- **Custom Metrics** - Calculator usage and performance metrics
- **Log Aggregation** - Centralized logging services
- **Performance Monitoring** - Application performance monitoring tools
