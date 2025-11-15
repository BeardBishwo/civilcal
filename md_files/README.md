# 🚀 **COMPLETE NEW MVC STRUCTURE** (Development Phase)

Since you're in development and not live, we can go **full MVC from scratch**! Here's the complete architecture:

## 📁 **COMPLETE PROJECT STRUCTURE**

```
Bishwo-calculator/                          ← Project Root
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
|
├── 📁 plugins/                      ← NEW: Plugin System
│   ├── 📁 calculator-plugins/       ← Additional calculators
│   │   ├── 📁 advanced-steel-design/
│   │   ├── 📁 green-building-tools/
│   │   └── ...
│   ├── 📁 theme-plugins/            ← Theme system
│   │   ├── 📁 dark-pro-theme/
│   │   ├── 📁 material-design-theme/
│   │   └── ...
│   └── plugin-manager.php
│
├── 📁 themes/                       ← NEW: Theme System
│   ├── 📁 default/                  ← Default theme
│   ├── 📁 professional/             ← Professional theme
│   └── theme-manager.php
|
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


=============================================================================

# 🚀 **PLUGIN & THEME SYSTEM ARCHITECTURE**

Perfect! Since you already have a massive calculator library (200+ calculators), let's build a **plugin system** that leverages your existing `modules/` structure while adding admin management capabilities.

## 🏗️ **SYSTEM ARCHITECTURE**

```
aec-calculator/
├── 📁 app/                          ← MVC Core
│   ├── 📁 Controllers/
│   ├── 📁 Models/
│   ├── 📁 Views/
│   ├── 📁 Core/
│   └── 📁 Services/
│
├── 📁 modules/                      ← EXISTING CALCULATORS (NO CHANGES!)
│   ├── civil/                       ← Your current structure stays
│   ├── electrical/                  ← Everything remains as is
│   └── ...
│
├── 📁 plugins/                      ← NEW: Plugin System
│   ├── 📁 calculator-plugins/       ← Additional calculators
│   │   ├── 📁 advanced-steel-design/
│   │   ├── 📁 green-building-tools/
│   │   └── ...
│   ├── 📁 theme-plugins/            ← Theme system
│   │   ├── 📁 dark-pro-theme/
│   │   ├── 📁 material-design-theme/
│   │   └── ...
│   └── plugin-manager.php
│
├── 📁 themes/                       ← NEW: Theme System
│   ├── 📁 default/                  ← Default theme
│   ├── 📁 professional/             ← Professional theme
│   └── theme-manager.php
│
├── 📁 public/                       ← Web root
└── 📁 config/                       ← Configuration
```

## 🔧 **PLUGIN SYSTEM IMPLEMENTATION**

### **1. Plugin Database Structure**

**Add these tables to your database:**
```sql
-- Plugins Table
CREATE TABLE plugins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    type ENUM('calculator', 'theme', 'integration') DEFAULT 'calculator',
    description TEXT,
    version VARCHAR(20) DEFAULT '1.0.0',
    author VARCHAR(255),
    author_url VARCHAR(255),
    
    -- Plugin Files
    plugin_path VARCHAR(500),
    main_file VARCHAR(255),
    
    -- Status
    is_active BOOLEAN DEFAULT FALSE,
    is_core BOOLEAN DEFAULT FALSE, -- Your existing modules are core
    
    -- Configuration
    settings JSON,
    requirements JSON,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_type (type),
    INDEX idx_active (is_active)
);

-- Themes Table
CREATE TABLE themes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    version VARCHAR(20) DEFAULT '1.0.0',
    author VARCHAR(255),
    
    -- Theme Files
    theme_path VARCHAR(500),
    screenshot VARCHAR(255),
    
    -- Status
    is_active BOOLEAN DEFAULT FALSE,
    is_default BOOLEAN DEFAULT FALSE,
    
    -- Styles
    styles JSON,
    settings JSON,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
);
```

### **2. Plugin Manager Service**

**`app/Services/PluginManager.php`**
```php
<?php
namespace App\Services;

class PluginManager {
    private $db;
    private $pluginsDir;
    
    public function __construct() {
        $this->db = new \App\Core\Database();
        $this->pluginsDir = BASE_PATH . '/plugins/calculator-plugins/';
    }
    
    /**
     * Scan and register all plugins
     */
    public function scanPlugins() {
        $plugins = [];
        
        // Scan plugin directories
        if (is_dir($this->pluginsDir)) {
            $pluginDirs = array_filter(glob($this->pluginsDir . '*'), 'is_dir');
            
            foreach ($pluginDirs as $pluginDir) {
                $pluginConfig = $this->loadPluginConfig($pluginDir);
                if ($pluginConfig) {
                    $plugins[] = $pluginConfig;
                }
            }
        }
        
        return $plugins;
    }
    
    /**
     * Load plugin configuration
     */
    private function loadPluginConfig($pluginDir) {
        $configFile = $pluginDir . '/plugin.json';
        
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            $config['plugin_path'] = $pluginDir;
            $config['slug'] = basename($pluginDir);
            return $config;
        }
        
        return null;
    }
    
    /**
     * Install a plugin (uploaded via admin)
     */
    public function installPlugin($zipFile) {
        // Extract zip to plugins directory
        $zip = new \ZipArchive();
        if ($zip->open($zipFile) === TRUE) {
            $pluginName = pathinfo($zipFile, PATHINFO_FILENAME);
            $extractPath = $this->pluginsDir . $pluginName;
            
            // Create directory
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0755, true);
            }
            
            // Extract files
            $zip->extractTo($extractPath);
            $zip->close();
            
            // Register in database
            return $this->registerPlugin($extractPath);
        }
        
        return false;
    }
    
    /**
     * Activate a plugin
     */
    public function activatePlugin($pluginSlug) {
        $plugin = $this->getPlugin($pluginSlug);
        
        if ($plugin && $this->checkRequirements($plugin)) {
            // Update database
            $stmt = $this->db->prepare("UPDATE plugins SET is_active = 1 WHERE slug = ?");
            $stmt->execute([$pluginSlug]);
            
            // Run activation hooks
            $this->runActivationHook($plugin);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get all active calculators (including plugins)
     */
    public function getActiveCalculators() {
        $calculators = [];
        
        // 1. Get core calculators (your existing modules)
        $calculators = array_merge($calculators, $this->getCoreCalculators());
        
        // 2. Get plugin calculators
        $calculators = array_merge($calculators, $this->getPluginCalculators());
        
        return $calculators;
    }
    
    /**
     * Get your existing modules as core calculators
     */
    private function getCoreCalculators() {
        $coreCalculators = [];
        $modulesPath = BASE_PATH . '/modules/';
        
        // Scan your existing modules directory
        $disciplines = ['civil', 'electrical', 'plumbing', 'hvac', 'fire', 'structural'];
        
        foreach ($disciplines as $discipline) {
            $disciplinePath = $modulesPath . $discipline;
            if (is_dir($disciplinePath)) {
                $categories = array_filter(glob($disciplinePath . '/*'), 'is_dir');
                
                foreach ($categories as $category) {
                    $calculators = array_filter(glob($category . '/*.php'), 'is_file');
                    
                    foreach ($calculators as $calculator) {
                        $coreCalculators[] = [
                            'type' => 'core',
                            'discipline' => $discipline,
                            'category' => basename($category),
                            'calculator' => pathinfo($calculator, PATHINFO_FILENAME),
                            'file_path' => $calculator,
                            'name' => $this->getCalculatorName($calculator)
                        ];
                    }
                }
            }
        }
        
        return $coreCalculators;
    }
}
?>
```

### **3. Plugin Configuration Format**

**Example: `plugins/calculator-plugins/advanced-steel-design/plugin.json`**
```json
{
    "name": "Advanced Steel Design",
    "slug": "advanced-steel-design",
    "type": "calculator",
    "description": "Advanced steel structure design and analysis tools",
    "version": "1.0.0",
    "author": "Steel Engineering Co.",
    "author_url": "https://steel-eng.com",
    
    "main_file": "steel-main.php",
    "calculators": {
        "beam-design": {
            "name": "Steel Beam Design",
            "description": "Design steel beams according to AISC standards",
            "category": "structural",
            "file": "beam-design.php"
        },
        "connection-design": {
            "name": "Steel Connection Design", 
            "description": "Design bolted and welded connections",
            "category": "structural",
            "file": "connection-design.php"
        }
    },
    
    "requirements": {
        "php_version": "7.4",
        "required_plugins": []
    },
    
    "settings": {
        "allow_custom_materials": true,
        "default_standard": "AISC"
    }
}
```

## 🎨 **THEME SYSTEM IMPLEMENTATION**

### **4. Theme Manager Service**

**`app/Services/ThemeManager.php`**
```php
<?php
namespace App\Services;

class ThemeManager {
    private $db;
    private $themesDir;
    
    public function __construct() {
        $this->db = new \App\Core\Database();
        $this->themesDir = BASE_PATH . '/themes/';
    }
    
    /**
     * Get active theme
     */
    public function getActiveTheme() {
        $stmt = $this->db->prepare("SELECT * FROM themes WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $theme = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$theme) {
            // Fallback to default theme
            $theme = $this->getDefaultTheme();
        }
        
        return $theme;
    }
    
    /**
     * Apply theme to views
     */
    public function applyTheme($viewPath, $data = []) {
        $activeTheme = $this->getActiveTheme();
        
        if ($activeTheme) {
            $themeViewPath = $this->themesDir . $activeTheme['slug'] . '/views/' . $viewPath;
            
            // If theme has this view, use it instead of default
            if (file_exists($themeViewPath)) {
                return $themeViewPath;
            }
        }
        
        // Fallback to default view
        return APP_PATH . '/Views/' . $viewPath;
    }
    
    /**
     * Get theme assets URL
     */
    public function getAssetUrl($assetPath) {
        $activeTheme = $this->getActiveTheme();
        
        if ($activeTheme) {
            $themeAssetPath = '/themes/' . $activeTheme['slug'] . '/assets/' . $assetPath;
            
            if (file_exists(BASE_PATH . $themeAssetPath)) {
                return $themeAssetPath;
            }
        }
        
        // Fallback to default assets
        return '/assets/' . $assetPath;
    }
}
?>
```

### **5. Enhanced View Class with Theme Support**

**`app/Core/View.php`**
```php
<?php
namespace App\Core;

class View {
    private $themeManager;
    
    public function __construct() {
        $this->themeManager = new \App\Services\ThemeManager();
    }
    
    public function render($view, $data = []) {
        // Apply theme to view path
        $viewPath = $this->themeManager->applyTheme($view . '.php', $data);
        
        // Extract data for the view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View {$viewPath} not found");
        }
        
        // Get the contents and clean buffer
        $content = ob_get_clean();
        
        // Apply theme layout
        $layoutPath = $this->themeManager->applyTheme('layouts/main.php', $data);
        
        if (file_exists($layoutPath)) {
            // Pass content to layout
            $data['content'] = $content;
            extract($data);
            
            include $layoutPath;
        } else {
            // No layout, just output content
            echo $content;
        }
    }
    
    /**
     * Get themed asset URL
     */
    public function asset($assetPath) {
        return $this->themeManager->getAssetUrl($assetPath);
    }
}
?>
```

## 🎯 **ADMIN PANEL INTEGRATION**

### **6. Plugin Management Controller**

**`app/Controllers/Admin/PluginController.php`**
```php
<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\PluginManager;

class PluginController extends Controller {
    private $pluginManager;
    
    public function __construct() {
        parent::__construct();
        $this->pluginManager = new PluginManager();
    }
    
    /**
     * Plugin management dashboard
     */
    public function index() {
        $plugins = $this->pluginManager->scanPlugins();
        $activeCalculators = $this->pluginManager->getActiveCalculators();
        
        $this->view('admin/plugins/index', [
            'title' => 'Plugin Management',
            'plugins' => $plugins,
            'activeCalculators' => $activeCalculators
        ]);
    }
    
    /**
     * Upload plugin via admin
     */
    public function upload() {
        if ($_FILES['plugin_zip']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['plugin_zip']['tmp_name'];
            
            if ($this->pluginManager->installPlugin($uploadedFile)) {
                $this->json(['success' => true, 'message' => 'Plugin installed successfully']);
            } else {
                $this->json(['success' => false, 'message' => 'Plugin installation failed']);
            }
        }
    }
    
    /**
     * Activate/deactivate plugin
     */
    public function toggle($pluginSlug, $action) {
        if ($action === 'activate') {
            $result = $this->pluginManager->activatePlugin($pluginSlug);
        } else {
            $result = $this->pluginManager->deactivatePlugin($pluginSlug);
        }
        
        if ($result) {
            $this->json(['success' => true, 'message' => "Plugin {$action}d successfully"]);
        } else {
            $this->json(['success' => false, 'message' => "Failed to {$action} plugin"]);
        }
    }
    
    /**
     * Delete plugin
     */
    public function delete($pluginSlug) {
        if ($this->pluginManager->deletePlugin($pluginSlug)) {
            $this->json(['success' => true, 'message' => 'Plugin deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete plugin']);
        }
    }
}
?>
```

### **7. Admin Plugin Management View**

**`app/Views/admin/plugins/index.php`**
```php
<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-puzzle-piece"></i> Plugin Management</h1>
        <p>Manage calculator plugins and extensions</p>
    </div>

    <!-- Upload Plugin Card -->
    <div class="card">
        <div class="card-header">
            <h3>Upload New Plugin</h3>
        </div>
        <div class="card-body">
            <form id="uploadPluginForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Plugin ZIP File</label>
                    <input type="file" name="plugin_zip" accept=".zip" required>
                    <small>Upload a plugin in ZIP format containing plugin.json</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload & Install
                </button>
            </form>
        </div>
    </div>

    <!-- Installed Plugins -->
    <div class="card">
        <div class="card-header">
            <h3>Installed Plugins</h3>
        </div>
        <div class="card-body">
            <div class="plugins-grid">
                <?php foreach ($plugins as $plugin): ?>
                <div class="plugin-card <?= $plugin['is_active'] ? 'active' : 'inactive' ?>">
                    <div class="plugin-header">
                        <h4><?= $plugin['name'] ?></h4>
                        <span class="plugin-version">v<?= $plugin['version'] ?></span>
                    </div>
                    
                    <p class="plugin-description"><?= $plugin['description'] ?></p>
                    
                    <div class="plugin-meta">
                        <span class="author">By: <?= $plugin['author'] ?></span>
                        <span class="type"><?= ucfirst($plugin['type']) ?></span>
                    </div>
                    
                    <div class="plugin-actions">
                        <?php if ($plugin['is_active']): ?>
                            <button class="btn btn-warning btn-sm" 
                                    onclick="togglePlugin('<?= $plugin['slug'] ?>', 'deactivate')">
                                <i class="fas fa-pause"></i> Deactivate
                            </button>
                        <?php else: ?>
                            <button class="btn btn-success btn-sm" 
                                    onclick="togglePlugin('<?= $plugin['slug'] ?>', 'activate')">
                                <i class="fas fa-play"></i> Activate
                            </button>
                        <?php endif; ?>
                        
                        <button class="btn btn-danger btn-sm" 
                                onclick="deletePlugin('<?= $plugin['slug'] ?>')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Active Calculators Overview -->
    <div class="card">
        <div class="card-header">
            <h3>Active Calculators</h3>
            <span class="badge"><?= count($activeCalculators) ?> calculators</span>
        </div>
        <div class="card-body">
            <div class="calculators-stats">
                <?php
                $categories = [];
                foreach ($activeCalculators as $calc) {
                    $categories[$calc['discipline']][] = $calc;
                }
                ?>
                
                <?php foreach ($categories as $discipline => $calculators): ?>
                <div class="discipline-stats">
                    <h4><?= ucfirst($discipline) ?></h4>
                    <span class="count"><?= count($calculators) ?> calculators</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function togglePlugin(slug, action) {
    fetch(`/admin/plugins/toggle/${slug}/${action}`, {
        method: 'POST',
        headers: {'X-CSRF-Token': '<?= $csrf_token ?>'}
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function deletePlugin(slug) {
    if (confirm('Are you sure you want to delete this plugin?')) {
        fetch(`/admin/plugins/delete/${slug}`, {
            method: 'POST',
            headers: {'X-CSRF-Token': '<?= $csrf_token ?>'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}
</script>
```

## 🛣️ **ROUTES FOR PLUGIN SYSTEM**

**`app/routes.php`**
```php
<?php
// Plugin Management Routes
$router->add('GET', '/admin/plugins', 'Admin\PluginController@index');
$router->add('POST', '/admin/plugins/upload', 'Admin\PluginController@upload');
$router->add('POST', '/admin/plugins/toggle/{slug}/{action}', 'Admin\PluginController@toggle');
$router->add('POST', '/admin/plugins/delete/{slug}', 'Admin\PluginController@delete');

// Theme Management Routes  
$router->add('GET', '/admin/themes', 'Admin\ThemeController@index');
$router->add('POST', '/admin/themes/activate/{slug}', 'Admin\ThemeController@activate');
$router->add('POST', '/admin/themes/delete/{slug}', 'Admin\ThemeController@delete');
?>
```

## 🚀 **MIGRATION STRATEGY**

### **Phase 1: Foundation (1 Week)**
1. Create database tables for plugins and themes
2. Implement PluginManager and ThemeManager services
3. Update View class for theme support

### **Phase 2: Admin Integration (1 Week)**
1. Create plugin/theme management controllers
2. Build admin interface for management
3. Test plugin upload/activation

### **Phase 3: Integration (1 Week)**
1. Integrate existing modules as "core plugins"
2. Test calculator loading from both core and plugins
3. Implement theme switching

## 🎯 **KEY BENEFITS:**

1. **✅ NO CHANGES TO EXISTING MODULES** - Your 200+ calculators stay exactly as they are
2. **🚀 PLUGIN SYSTEM** - Add new calculators via admin panel
3. **🎨 THEME SYSTEM** - Change look and feel easily
4. **🔧 ADMIN MANAGEMENT** - Upload, activate, deactivate, delete from admin
5. **📈 SCALABLE** - Easy to extend with new features

Your existing `modules/` structure becomes **"core plugins"** that are automatically registered and can be managed alongside new plugins! 🚀


==========================================

## Complete Modules File Structure - Bishwo Calculator

Here is the **complete modules file structure** from your existing Bishwo_Calculator project:

### **📁 CIVIL ENGINEERING MODULES**
```
modules/civil/
├── brickwork/
│   ├── brick-quantity.php
│   ├── mortar-ratio.php
│   └── plastering-estimator.php
├── concrete/
│   ├── concrete-mix.php
│   ├── concrete-strength.php
│   ├── concrete-volume.php
│   └── rebar-calculation.php
├── earthwork/
│   ├── cut-and-fill-volume.php
│   ├── excavation-volume.php
│   └── slope-calculation.php
├── resources/
│   └── css/
└── structural/
    ├── beam-load-capacity.php
    ├── column-design.php
    ├── foundation-design.php
    └── slab-design.php
```

### **⚡ ELECTRICAL ENGINEERING MODULES**
```
modules/electrical/
├── conduit-sizing/
│   ├── cable-tray-sizing.php
│   ├── conduit-fill-calculation.php
│   ├── entrance-service-sizing.php
│   └── junction-box-sizing.php
├── load-calculation/
│   ├── arc-flash-boundary.php
│   ├── battery-load-bank-sizing.php
│   ├── demand-load-calculation.php
│   ├── feeder-sizing.php
│   ├── general-lighting-load.php
│   ├── motor-full-load-amps.php
│   ├── ocpd-sizing.php
│   ├── panel-schedule.php
│   └── receptacle-load.php
├── short-circuit/
│   ├── available-fault-current.php
│   ├── ground-conductor-sizing.php
│   └── power-factor-correction.php
├── voltage-drop/
│   ├── single-phase-voltage-drop.php
│   ├── three-phase-voltage-drop.php
│   ├── voltage-drop-sizing.php
│   └── voltage-regulation.php
└── wire-sizing/
    ├── motor-circuit-wire-sizing.php
    ├── motor-circuit-wiring.php
    ├── transformer-kva-sizing.php
    ├── wire-ampacity.php
    └── wire-size-by-current.php
```

### **💰 ESTIMATION MODULES**
```
modules/estimation/
├── cost-estimation/
│   ├── boq-preparation.php
│   ├── contingency-overheads.php
│   ├── cost-escalation.php
│   ├── item-rate-analysis.php
│   └── project-cost-summary.php
├── equipment-estimation/
│   ├── equipment-allocation.php
│   ├── equipment-hourly-rate.php
│   ├── fuel-consumption.php
│   └── machinery-usage.php
├── labor-estimation/
│   ├── labor-cost-estimator.php
│   ├── labor-hour-calculation.php
│   ├── labor-rate-analysis.php
│   └── manpower-requirement.php
├── material-estimation/
│   ├── concrete-materials.php
│   ├── masonry-materials.php
│   ├── paint-materials.php
│   ├── plaster-materials.php
│   └── tile-materials.php
├── project-financials/
│   ├── break-even-analysis.php
│   ├── cash-flow-analysis.php
│   ├── npv-irr-analysis.php
│   ├── payback-period.php
│   └── profit-loss-analysis.php
├── quantity-takeoff/
│   ├── brickwork-quantity.php
│   ├── concrete-quantity.php
│   ├── flooring-quantity.php
│   ├── formwork-quantity.php
│   ├── paint-quantity.php
│   ├── plaster-quantity.php
│   └── rebar-quantity.php
├── reports/
│   ├── detailed-boq-report.php
│   ├── equipment-cost-report.php
│   ├── financial-dashboard.php
│   ├── labor-cost-report.php
│   ├── material-cost-report.php
│   └── summary-report.php
└── tender-bidding/
    ├── bid-price-comparison.php
    ├── bid-sheet-generator.php
    ├── pre-bid-analysis.php
    └── rate-deviation.php
```

### **🔥 FIRE PROTECTION MODULES**
```
modules/fire/
├── fire-pumps/
│   ├── driver-power.php
│   ├── jockey-pump.php
│   └── pump-sizing.php
├── hazard-classification/
│   ├── commodity-classification.php
│   ├── design-density.php
│   └── occupancy-assessment.php
├── hydraulics/
│   └── hazen-williams.php
├── sprinklers/
│   ├── discharge-calculations.php
│   ├── pipe-sizing.php
│   └── sprinkler-layout.php
└── standpipes/
    ├── hose-demand.php
    ├── pressure-calculations.php
    └── standpipe-classification.php
```

### **❄️ HVAC MODULES**
```
modules/hvac/
├── duct-sizing/
│   ├── equivalent-duct.php
│   ├── fitting-loss.php
│   ├── grille-sizing.php
│   ├── pressure-drop.php
│   └── velocity-sizing.php
├── energy-analysis/
│   ├── co2-emissions.php
│   ├── energy-consumption.php
│   ├── insulation-savings.php
│   └── payback-period.php
├── equipment-sizing/
│   ├── ac-sizing.php
│   ├── chiller-sizing.php
│   ├── furnace-sizing.php
│   └── pump-sizing.php
├── load-calculation/
│   ├── cooling-load.php
│   ├── heating-load.php
│   ├── infiltration.php
│   └── ventilation.php
└── psychrometrics/
    ├── air-properties.php
    ├── cooling-load-psych.php
    ├── enthalpy.php
    └── sensible-heat-ratio.php
```

### **🔧 MEP MODULES**
```
modules/mep/
├── bootstrap.php
├── coordination/
├── cost-management/
├── data-utilities/
├── electrical/
├── energy-efficiency/
├── fire-protection/
├── integration/
├── mechanical/
├── plumbing/
└── reports-documentation/
```

### **💧 PLUMBING MODULES**
```
modules/plumbing/
├── drainage/
│   ├── drainage-pipe-sizing.php
│   ├── grease-trap-sizing.php
│   ├── soil-stack-sizing.php
│   ├── storm-drainage.php
│   ├── trap-sizing.php
│   └── vent-pipe-sizing.php
├── fixtures/
│   ├── fixture-unit-calculation.php
│   ├── shower-sizing.php
│   ├── sink-sizing.php
│   └── toilet-flow.php
├── hot_water/
│   ├── heat-loss-calculation.php
│   ├── recirculation-loop.php
│   ├── safety-valve.php
│   ├── storage-tank-sizing.php
│   └── water-heater-sizing.php
├── pipe_sizing/
│   ├── expansion-loop-sizing.php
│   ├── gas-pipe-sizing.php
│   ├── pipe-flow-capacity.php
│   └── water-pipe-sizing.php
├── stormwater/
│   ├── downpipe-sizing.php
│   ├── gutter-sizing.php
│   ├── pervious-area.php
│   └── stormwater-storage.php
└── water_supply/
    ├── cold-water-demand.php
    ├── hot-water-demand.php
    ├── main-isolation-valve.php
    ├── pressure-loss.php
    ├── pump-sizing.php
    ├── storage-tank-sizing.php
    ├── water-demand-calculation.php
    └── water-hammer-calculation.php
```

### **📊 PROJECT MANAGEMENT MODULES**
```
modules/project-management/
├── analytics/
├── communication/
├── dashboard/
├── documents/
├── financial/
├── integration/
├── procurement/
├── quality/
├── reports/
├── resources/
├── scheduling/
└── settings/
```

### **🏗️ SITE ENGINEERING MODULES**
```
modules/site/
├── concrete-tools/
│   ├── placement-rate.php
│   ├── temperature-control.php
│   ├── testing-requirements.php
│   └── yardage-adjustments.php
├── earthwork/
│   ├── cut-fill-balancing.php
│   ├── equipment-production.php
│   ├── slope-paving.php
│   ├── swelling-shrink.php
│   └── swelling-shrinkage.php
├── productivity/
│   ├── cost-productivity.php
│   ├── equipment-utilization.php
│   ├── labor-productivity.php
│   └── schedule-compression.php
├── safety/
│   ├── crane-setup.php
│   ├── evacuation-planning.php
│   ├── fall-protection.php
│   └── trench-safety.php
└── surveying/
    ├── batter-boards.php
    ├── grade-rod.php
    ├── horizontal-curve-staking.php
    └── slope-staking.php
```

### **🏢 STRUCTURAL ENGINEERING MODULES**
```
modules/structural/
├── beam-analysis/
│   ├── beam-design.php
│   ├── beam-load-combination.php
│   ├── cantilever-beam.php
│   ├── continuous-beam.php
│   └── simply-supported-beam.php
├── column-design/
│   ├── biaxial-column.php
│   ├── column-footing-link.php
│   ├── long-column.php
│   ├── short-column.php
│   └── steel-column-design.php
├── foundation-design/
│   ├── combined-footing.php
│   ├── foundation-pressure.php
│   ├── isolated-footing.php
│   ├── pile-foundation.php
│   └── raft-foundation.php
├── load-analysis/
│   ├── dead-load.php
│   ├── live-load.php
│   ├── load-combination.php
│   ├── seismic-load.php
│   └── wind-load.php
├── reinforcement/
│   ├── bar-bending-schedule.php
│   ├── detailing-drawing.php
│   ├── rebar-anchorage.php
│   ├── reinforcement-optimizer.php
│   └── stirrup-design.php
├── reports/
│   ├── beam-report.php
│   ├── column-report.php
│   ├── foundation-report.php
│   ├── full-structure-summary.php
│   └── load-analysis-summary.php
├── slab-design/
│   ├── flat-slab.php
│   ├── one-way-slab.php
│   ├── slab-load-calculation.php
│   ├── two-way-slab.php
│   └── waffle-slab.php
└── steel-structure/
    ├── connection-design.php
    ├── purlin-design.php
    ├── steel-base-plate.php
    ├── steel-beam-design.php
    └── steel-truss-analysis.php
```

**📈 TOTALS:**
- **Total Calculator Files:** 200+ individual calculator modules
- **Main Categories:** 10 engineering disciplines
- **Sub-categories:** 50+ specialized calculation areas
- **Ready for MVC Integration:** All files are now ready to be integrated into the new MVC structure

This comprehensive module structure will be seamlessly integrated with your new MVC framework!
