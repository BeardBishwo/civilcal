# 🚀 **COMPLETE NEW MVC STRUCTURE** (Development Phase)

Since you're in development and not live, we can go **full MVC from scratch**! Here's the complete architecture:

## 📁 **COMPLETE PROJECT STRUCTURE**

```
aec-calculator/                          ← Project Root
├── 📁 app/                             ← MVC Application Core
│   ├── 📁 Controllers/                 ← All Controllers
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── SettingsController.php
│   │   │   └── ModuleController.php
│   │   ├── AuthController.php
│   │   ├── CalculatorController.php
│   │   ├── UserController.php
│   │   └── ApiController.php
│   │
│   ├── 📁 Models/                      ← All Models
│   │   ├── User.php
│   │   ├── Calculation.php
│   │   ├── Project.php
│   │   ├── Subscription.php
│   │   └── Settings.php
│   │
│   ├── 📁 Views/                       ← All Views (Templates)
│   │   ├── 📁 layouts/
│   │   │   ├── main.php
│   │   │   ├── admin.php
│   │   │   └── auth.php
│   │   ├── 📁 admin/
│   │   │   ├── dashboard.php
│   │   │   ├── users/
│   │   │   └── settings/
│   │   ├── 📁 auth/
│   │   │   ├── login.php
│   │   │   ├── register.php
│   │   │   └── forgot-password.php
│   │   ├── 📁 calculators/
│   │   │   ├── civil/
│   │   │   ├── electrical/
│   │   │   ├── plumbing/
│   │   │   └── ...
│   │   └── 📁 partials/
│   │       ├── header.php
│   │       ├── footer.php
│   │       └── navigation.php
│   │
│   ├── 📁 Core/                        ← Framework Core
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   ├── View.php
│   │   ├── Database.php
│   │   ├── Auth.php
│   │   └── Validator.php
│   │
│   ├── 📁 Middleware/                  ← Middleware
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   └── CorsMiddleware.php
│   │
│   ├── 📁 Services/                    ← Business Logic
│   │   ├── CalculatorService.php
│   │   ├── PaymentService.php
│   │   ├── EmailService.php
│   │   └── FileService.php
│   │
│   ├── 📁 Calculators/                 ← Calculator Engines
│   │   ├── CivilCalculator.php
│   │   ├── ElectricalCalculator.php
│   │   ├── PlumbingCalculator.php
│   │   ├── HvacCalculator.php
│   │   └── BaseCalculator.php
│   │
│   └── bootstrap.php                   ← Application Bootstrap
│
├── 📁 public/                          ← Web Root (Publicly Accessible)
│   ├── index.php                       ← Front Controller
│   ├── 📁 assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── uploads/
│   └── .htaccess
│
├── 📁 modules/                         ← Your Existing Calculator Modules
│   ├── civil/
│   ├── electrical/
│   ├── plumbing/
│   ├── hvac/
│   ├── fire/
│   └── ...
│
├── 📁 config/                          ← Configuration
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   └── services.php
│
├── 📁 storage/                         ← Storage
│   ├── logs/
│   ├── cache/
│   ├── sessions/
│   └── backups/
│
├── 📁 tests/                           ← Tests
│   ├── Unit/
│   ├── Feature/
│   └── ...
│
├── 📁 vendor/                          ← Composer Dependencies
├── composer.json
├── .env.example
└── README.md
```

## 🏗️ **ARCHITECTURE DIAGRAM**

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT REQUESTS                          │
│  GET /calculators/civil/concrete-volume                     │
│  POST /api/calculate                                        │
│  GET /admin/users                                           │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    PUBLIC/INDEX.PHP                         │
│  • Front Controller                                         │
│  • Bootstrap Application                                   │
│  • Handle All Requests                                     │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    APP/CORE/ROUTER.PHP                      │
│  • Route Matching                                          │
│  • Middleware Execution                                    │
│  • Controller Dispatching                                  │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    MIDDLEWARE STACK                         │
│  • CORS Handling                                           │
│  • Authentication                                          │
│  • Authorization                                           │
│  • CSRF Protection                                         │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLLERS                              │
│  • Handle HTTP Requests                                    │
│  • Validate Input                                          │
│  • Call Services/Models                                    │
│  • Return Responses                                        │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    SERVICES & MODELS                       │
│  • Business Logic                                          │
│  • Data Manipulation                                       │
│  • Database Operations                                     │
│  • Calculator Engines                                      │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    VIEWS (TEMPLATES)                       │
│  • HTML Rendering                                          │
│  • Data Presentation                                       │
│  • Layout Management                                       │
└─────────────────────────────────────────────────────────────┘
```

## 🔧 **CORE FILES IMPLEMENTATION**

### **1. Front Controller (`public/index.php`)**
```php
<?php
// Front Controller - All requests go through this file

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;

// Start session
session_start();

// Initialize router
$router = new Router();

// Load routes
require_once __DIR__ . '/../app/routes.php';

// Dispatch the request
$router->dispatch();
?>
```

### **2. Application Bootstrap (`app/bootstrap.php`)**
```php
<?php
// Define base path
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_PATH . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
require_once BASE_PATH . '/config/app.php';
?>
```

### **3. Router (`app/Core/Router.php`)**
```php
<?php
namespace App\Core;

class Router {
    protected $routes = [];
    protected $middleware = [];
    
    public function add($method, $uri, $controller, $middleware = []) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $uri, $method)) {
                return $this->callRoute($route);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }
    
    protected function matchRoute($route, $uri, $method) {
        // Convert route URI to regex pattern
        $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route['uri']);
        $pattern = "#^$pattern$#";
        
        return $route['method'] === $method && preg_match($pattern, $uri, $matches);
    }
    
    protected function callRoute($route) {
        // Execute middleware
        foreach ($route['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            if (!$middleware->handle()) {
                return; // Middleware blocked the request
            }
        }
        
        // Parse controller@method
        list($controllerClass, $method) = explode('@', $route['controller']);
        $controllerClass = "App\\Controllers\\{$controllerClass}";
        
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            call_user_func([$controller, $method]);
        }
    }
}
?>
```

### **4. Base Controller (`app/Core/Controller.php`)**
```php
<?php
namespace App\Core;

class Controller {
    protected $db;
    protected $auth;
    
    public function __construct() {
        $this->db = new Database();
        $this->auth = new Auth();
    }
    
    protected function view($view, $data = []) {
        $view = new View();
        $view->render($view, $data);
    }
    
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
}
?>
```

### **5. Routes Definition (`app/routes.php`)**
```php
<?php
// Authentication Routes
$router->add('GET', '/login', 'AuthController@showLogin', ['guest']);
$router->add('POST', '/login', 'AuthController@login', ['guest']);
$router->add('GET', '/register', 'AuthController@showRegister', ['guest']);
$router->add('POST', '/register', 'AuthController@register', ['guest']);
$router->add('POST', '/logout', 'AuthController@logout', ['auth']);

// Calculator Routes
$router->add('GET', '/', 'CalculatorController@dashboard', ['auth']);
$router->add('GET', '/calculators', 'CalculatorController@index', ['auth']);
$router->add('GET', '/calculators/{category}', 'CalculatorController@category', ['auth']);
$router->add('GET', '/calculators/{category}/{calculator}', 'CalculatorController@show', ['auth']);
$router->add('POST', '/api/calculate/{calculator}', 'ApiController@calculate', ['auth']);

// User Routes
$router->add('GET', '/profile', 'UserController@profile', ['auth']);
$router->add('POST', '/profile', 'UserController@updateProfile', ['auth']);

// Admin Routes
$router->add('GET', '/admin', 'Admin\\DashboardController@index', ['auth', 'admin']);
$router->add('GET', '/admin/users', 'Admin\\UserController@index', ['auth', 'admin']);
$router->add('GET', '/admin/settings', 'Admin\\SettingsController@index', ['auth', 'admin']);
$router->add('POST', '/admin/settings', 'Admin\\SettingsController@update', ['auth', 'admin']);
?>
```

### **6. Calculator Controller (`app/Controllers/CalculatorController.php`)**
```php
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\CalculatorService;

class CalculatorController extends Controller {
    protected $calculatorService;
    
    public function __construct() {
        parent::__construct();
        $this->calculatorService = new CalculatorService();
    }
    
    public function dashboard() {
        $recentCalculations = $this->calculatorService->getRecentCalculations($this->auth->user()['id']);
        $favoriteCalculators = $this->calculatorService->getFavoriteCalculators($this->auth->user()['id']);
        
        $this->view('calculators/dashboard', [
            'title' => 'Engineering Calculator Dashboard',
            'recentCalculations' => $recentCalculations,
            'favoriteCalculators' => $favoriteCalculators
        ]);
    }
    
    public function index() {
        $categories = $this->calculatorService->getAllCategories();
        
        $this->view('calculators/index', [
            'title' => 'All Calculators',
            'categories' => $categories
        ]);
    }
    
    public function category($category) {
        $calculators = $this->calculatorService->getCalculatorsByCategory($category);
        
        $this->view('calculators/category', [
            'title' => ucfirst($category) . ' Calculators',
            'category' => $category,
            'calculators' => $calculators
        ]);
    }
    
    public function show($category, $calculator) {
        $calculatorData = $this->calculatorService->getCalculator($category, $calculator);
        
        $this->view('calculators/show', [
            'title' => $calculatorData['name'],
            'category' => $category,
            'calculator' => $calculator,
            'calculatorData' => $calculatorData
        ]);
    }
}
?>
```

### **7. Main Layout (`app/Views/layouts/main.php`)**
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AEC Calculator' ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include APP_PATH . '/Views/partials/header.php'; ?>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <?php include APP_PATH . '/Views/partials/footer.php'; ?>
    
    <script src="/assets/js/app.js"></script>
</body>
</html>
```

### **8. Calculator Service (`app/Services/CalculatorService.php`)**
```php
<?php
namespace App\Services;

use App\Core\Database;
use App\Calculators\CivilCalculator;
use App\Calculators\ElectricalCalculator;

class CalculatorService {
    protected $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAllCategories() {
        return [
            'civil' => [
                'name' => 'Civil Engineering',
                'icon' => 'fa-hard-hat',
                'description' => 'Structural, concrete, earthwork calculations'
            ],
            'electrical' => [
                'name' => 'Electrical Engineering', 
                'icon' => 'fa-bolt',
                'description' => 'Load calculations, circuit design'
            ],
            // ... more categories
        ];
    }
    
    public function getCalculatorsByCategory($category) {
        $calculators = [
            'civil' => [
                'concrete-volume' => [
                    'name' => 'Concrete Volume Calculator',
                    'description' => 'Calculate concrete volume for slabs, beams, columns',
                    'icon' => 'fa-cube',
                    'inputs' => [
                        'length' => ['type' => 'number', 'label' => 'Length', 'unit' => 'm'],
                        'width' => ['type' => 'number', 'label' => 'Width', 'unit' => 'm'],
                        'height' => ['type' => 'number', 'label' => 'Height', 'unit' => 'm']
                    ]
                ],
                'rebar-calculation' => [
                    'name' => 'Rebar Calculation',
                    'description' => 'Calculate rebar requirements for concrete structures',
                    'icon' => 'fa-bars',
                    'inputs' => [
                        // ... input definitions
                    ]
                ]
            ],
            // ... other categories
        ];
        
        return $calculators[$category] ?? [];
    }
    
    public function calculate($category, $calculator, $inputs) {
        $calculatorClass = $this->getCalculatorClass($category);
        
        if ($calculatorClass && method_exists($calculatorClass, $calculator)) {
            return $calculatorClass->$calculator($inputs);
        }
        
        throw new \Exception("Calculator not found: {$category}/{$calculator}");
    }
    
    protected function getCalculatorClass($category) {
        $classes = [
            'civil' => CivilCalculator::class,
            'electrical' => ElectricalCalculator::class,
            // ... other calculators
        ];
        
        return $classes[$category] ?? null;
    }
}
?>
```

## 🎯 **MIGRATION STRATEGY**

### **Phase 1: Foundation (Week 1)**
1. Create the MVC directory structure
2. Implement core classes (Router, Controller, Model, View)
3. Set up basic routing and authentication

### **Phase 2: Calculators (Week 2)**
1. Create calculator services and engines
2. Build calculator controllers and views
3. Migrate your existing modules/ calculators

### **Phase 3: Admin & Features (Week 3)**
1. Build admin panel with MVC
2. Implement user management
3. Add subscription system

### **Phase 4: Polish (Week 4)**
1. Add middleware for security
2. Implement API endpoints
3. Add testing and documentation

## 🚀 **IMMEDIATE START**

**Execute these commands:**
```bash
# Create the new structure
mkdir -p aec-calculator/{app/{Controllers,Models,Views,Core,Middleware,Services,Calculators},public/assets,config,storage,modules}

# Create essential files
touch public/index.php
touch app/bootstrap.php
touch app/routes.php
touch app/Core/{Router,Controller,Model,View,Database}.php
```

This gives you a **professional, scalable MVC architecture** that can grow with your application! Want me to implement any specific part first?