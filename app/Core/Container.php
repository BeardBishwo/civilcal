<?php
namespace App\Core;

/**
 * Simple Dependency Injection Container
 * Provides service registration, resolution, and management
 */
class Container {
    private array $bindings = [];
    private array $instances = [];
    private array $shared = [];
    
    /**
     * Register a binding with the container
     */
    public function bind(string $abstract, \Closure $concrete = null, bool $shared = false): void {
        if (is_null($concrete)) {
            $concrete = fn($container) => $container->build($abstract);
        }
        
        $this->bindings[$abstract] = $concrete;
        $this->shared[$abstract] = $shared;
    }
    
    /**
     * Register a shared (singleton) binding
     */
    public function singleton(string $abstract, \Closure $concrete = null): void {
        $this->bind($abstract, $concrete, true);
    }
    
    /**
     * Register an instance as shared
     */
    public function instance(string $abstract, mixed $instance): mixed {
        $this->instances[$abstract] = $instance;
        $this->shared[$abstract] = true;
        return $instance;
    }
    
    /**
     * Resolve a service from the container
     */
    public function make(string $abstract, array $parameters = []): mixed {
        if ($this->isShared($abstract)) {
            return $this->getSharedInstance($abstract);
        }
        
        return $this->resolve($abstract, $parameters);
    }
    
    /**
     * Check if a service is shared
     */
    public function isShared(string $abstract): bool {
        return isset($this->instances[$abstract]) || 
               (isset($this->shared[$abstract]) && $this->shared[$abstract]);
    }
    
    /**
     * Get a shared instance
     */
    private function getSharedInstance(string $abstract): mixed {
        if (!isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $this->resolve($abstract);
        }
        
        return $this->instances[$abstract];
    }
    
    /**
     * Resolve a service instance
     */
    private function resolve(string $abstract, array $parameters = []): mixed {
        // Check if we have a binding
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];
            $object = $concrete($this, $parameters);
        } else {
            // No binding found, try to build the class
            $object = $this->build($abstract, $parameters);
        }
        
        return $object;
    }
    
    /**
     * Build a concrete instance
     */
    public function build(string $concrete, array $parameters = []): mixed {
        // If already an instance, return it
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }
        
        // Get reflection
        try {
            $reflector = new \ReflectionClass($concrete);
        } catch (\ReflectionException $e) {
            throw new \Exception("Class {$concrete} does not exist", 0, $e);
        }
        
        // Check if class is instantiable
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable");
        }
        
        // Get constructor
        $constructor = $reflector->getConstructor();
        
        if (is_null($constructor)) {
            return new $concrete;
        }
        
        // Get constructor parameters
        $parameters = $this->getDependencies($constructor);
        
        return $reflector->newInstanceArgs($parameters);
    }
    
    /**
     * Get dependencies for a constructor
     */
    private function getDependencies(\ReflectionMethod $constructor): array {
        $parameters = $constructor->getParameters();
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType();
            
            if ($dependency instanceof \ReflectionNamedType) {
                $dependencyName = $dependency->getName();
                
                // Handle built-in types
                if ($dependency->isBuiltin()) {
                    $dependencies[] = $this->resolvePrimitive($parameter);
                } else {
                    $dependencies[] = $this->make($dependencyName);
                }
            } else {
                $dependencies[] = $this->resolvePrimitive($parameter);
            }
        }
        
        return $dependencies;
    }
    
    /**
     * Resolve primitive dependencies
     */
    private function resolvePrimitive(\ReflectionParameter $parameter): mixed {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        
        throw new \Exception("Unresolvable dependency {$parameter->getName()}");
    }
    
    /**
     * Check if a binding exists
     */
    public function has(string $abstract): bool {
        return isset($this->bindings[$abstract]);
    }
    
    /**
     * Forget a binding
     */
    public function forget(string $abstract): void {
        unset($this->bindings[$abstract], $this->instances[$abstract]);
    }
    
    /**
     * Get all bindings
     */
    public function getBindings(): array {
        return $this->bindings;
    }
    
    /**
     * Register core services
     */
    public function registerCoreServices(): void {
        // Database service
        $this->singleton('Database', fn() => \App\Core\EnhancedDatabase::getInstance());
        
        // Logger service
        $this->singleton('Logger', fn($container) => new \App\Services\Logger());
        
        // Auth service
        $this->singleton('Auth', fn($container) => new \App\Core\Auth());
        
        // View service
        $this->singleton('View', fn($container) => new \App\Core\View());
        
        // Theme service
        $this->singleton('ThemeManager', fn($container) => new \App\Services\ThemeManager());
        
        // Calculator service
        $this->singleton('CalculatorService', fn($container) => new \App\Services\CalculatorService(
            $container->make('Database'),
            $container->make('Logger'),
            $container->make('Cache')
        ));
        
        // Cache service
        $this->singleton('Cache', fn($container) => new \App\Services\Cache());
        
        // Email service
        $this->singleton('EmailService', fn($container) => new \App\Services\EmailService(
            $container->make('Logger')
        ));
    }
    
    /**
     * Register all services
     */
    public static function create(): self {
        $container = new self();
        $container->registerCoreServices();
        return $container;
    }
}
