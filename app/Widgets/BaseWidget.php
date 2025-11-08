<?php

namespace App\Widgets;

/**
 * Abstract Base Widget Class
 * 
 * Provides common functionality for all widgets in the system.
 * This is the foundation for the widget system architecture.
 */
abstract class BaseWidget
{
    protected string $widgetId;
    protected string $title;
    protected string $description;
    protected bool $isEnabled = true;
    protected array $config = [];
    protected array $cssClasses = [];
    protected string $widgetType = 'default';

    public function __construct(array $config = [])
    {
        $this->widgetId = $this->generateWidgetId();
        $this->config = $config;
        $this->initialize();
    }

    /**
     * Initialize widget (to be implemented by child classes)
     */
    protected function initialize(): void
    {
        // Child classes can override this method
    }

    /**
     * Generate unique widget ID
     */
    private function generateWidgetId(): string
    {
        return strtolower(str_replace(['\\', '_'], '-', static::class)) . '-' . uniqid();
    }

    /**
     * Get widget metadata
     */
    public function getMetadata(): array
    {
        return [
            'id' => $this->widgetId,
            'type' => $this->widgetType,
            'title' => $this->title,
            'description' => $this->description,
            'is_enabled' => $this->isEnabled,
            'css_classes' => $this->cssClasses,
            'config' => $this->config,
            'version' => $this->getVersion(),
            'author' => $this->getAuthor(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }

    /**
     * Get widget title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get widget description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get widget default title (to be overridden by child classes)
     */
    protected function getDefaultTitle(): string
    {
        return 'Widget Title';
    }

    /**
     * Get widget default description (to be overridden by child classes)
     */
    protected function getDefaultDescription(): string
    {
        return 'Widget description';
    }

    /**
     * Check if widget is enabled
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Enable/disable widget
     */
    public function setEnabled(bool $enabled): void
    {
        $this->isEnabled = $enabled;
    }

    /**
     * Get CSS classes
     */
    public function getCssClasses(): string
    {
        return implode(' ', $this->cssClasses);
    }

    /**
     * Add CSS class
     */
    public function addCssClass(string $class): void
    {
        if (!in_array($class, $this->cssClasses)) {
            $this->cssClasses[] = $class;
        }
    }

    /**
     * Remove CSS class
     */
    public function removeCssClass(string $class): void
    {
        $this->cssClasses = array_filter($this->cssClasses, fn($c) => $c !== $class);
    }

    /**
     * Get configuration
     */
    public function getConfig(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        return $this->config[$key] ?? $default;
    }

    /**
     * Set configuration
     */
    public function setConfig(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Get widget type
     */
    public function getType(): string
    {
        return $this->widgetType;
    }

    /**
     * Render widget HTML
     */
    abstract public function render(array $data = []): string;

    /**
     * Get widget assets (CSS, JS files)
     */
    public function getAssets(): array
    {
        return [
            'css' => [],
            'js' => []
        ];
    }

    /**
     * Get widget dependencies
     */
    public function getDependencies(): array
    {
        return [];
    }

    /**
     * Validate widget configuration
     */
    public function validateConfig($config = null): bool
    {
        // Base validation - child classes can override
        return true;
    }

    /**
     * Get widget version
     */
    protected function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Get widget author
     */
    protected function getAuthor(): string
    {
        return 'Bishwo Calculator';
    }

    /**
     * Get creation timestamp
     */
    protected function getCreatedAt(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get update timestamp
     */
    protected function getUpdatedAt(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get widget JavaScript data
     */
    public function getJavaScriptData(): array
    {
        return [
            'widgetId' => $this->widgetId,
            'type' => $this->widgetType,
            'config' => $this->config
        ];
    }

    /**
     * Get widget HTML attributes
     */
    public function getAttributes(): array
    {
        $attributes = [
            'id' => $this->widgetId,
            'class' => $this->getCssClasses(),
            'data-widget-type' => $this->widgetType,
            'data-enabled' => $this->isEnabled ? 'true' : 'false'
        ];

        // Add data attributes from config
        foreach ($this->config as $key => $value) {
            $attributes['data-' . str_replace('_', '-', $key)] = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
        }

        return $attributes;
    }

    /**
     * Convert attributes to HTML string
     */
    protected function attributesToString(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $key => $value) {
            if ($value !== null && $value !== '') {
                $html .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            }
        }
        return $html;
    }

    /**
     * Get widget statistics
     */
    public function getStats(): array
    {
        return [
            'render_count' => 0,
            'last_rendered' => null,
            'error_count' => 0,
            'average_render_time' => 0
        ];
    }

    /**
     * Widget lifecycle method - called before rendering
     */
    public function beforeRender(): void
    {
        // Child classes can override this method
    }

    /**
     * Widget lifecycle method - called after rendering
     */
    public function afterRender(): void
    {
        // Child classes can override this method
    }

    /**
     * Get widget health status
     */
    public function getHealthStatus(): array
    {
        return [
            'status' => 'healthy',
            'enabled' => $this->isEnabled,
            'configuration_valid' => $this->validateConfig(),
            'dependencies_met' => empty(array_diff($this->getDependencies(), $this->getAvailableDependencies())),
            'last_check' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Get available dependencies (override in child classes)
     */
    protected function getAvailableDependencies(): array
    {
        return [];
    }

    /**
     * Get widget permissions
     */
    public function getPermissions(): array
    {
        return [
            'view' => true,
            'edit' => true,
            'delete' => true,
            'configure' => true
        ];
    }

    /**
     * Check if user has permission for widget action
     */
    public function hasPermission(string $action, array $userPermissions = []): bool
    {
        $permissions = $this->getPermissions();
        return $permissions[$action] ?? false;
    }

    /**
     * Clone widget
     */
    public function clone(): self
    {
        $cloned = clone $this;
        $cloned->widgetId = $this->generateWidgetId();
        return $cloned;
    }

    /**
     * Convert widget to array
     */
    public function toArray(): array
    {
        return [
            'metadata' => $this->getMetadata(),
            'config' => $this->config,
            'css_classes' => $this->cssClasses,
            'stats' => $this->getStats(),
            'health_status' => $this->getHealthStatus(),
            'permissions' => $this->getPermissions()
        ];
    }

    /**
     * Get widget cache key
     */
    public function getCacheKey(): string
    {
        return 'widget_' . $this->widgetId . '_' . md5(serialize($this->config));
    }

    /**
     * Get widget cache TTL (seconds)
     */
    public function getCacheTtl(): int
    {
        return 3600; // 1 hour
    }

    /**
     * Cache widget content
     */
    public function cache(string $content): void
    {
        // Implementation depends on caching system
        // This is a placeholder method
    }

    /**
     * Get cached content
     */
    public function getCachedContent(): ?string
    {
        // Implementation depends on caching system
        // This is a placeholder method
        return null;
    }
    
    /**
     * Create widget from array data
     * 
     * @param array $data
     * @return static|null
     */
    public static function fromArray(array $data)
    {
        return new static($data['config'] ?? []);
    }
    
    /**
     * Get widget ID
     */
    public function getId(): string
    {
        return $this->widgetId;
    }
    
    /**
     * Get widget position
     */
    public function getPosition(): int
    {
        return $this->getConfig('position', 0);
    }
}
?>
