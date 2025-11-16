<?php
namespace App\Core\Exceptions;

/**
 * Custom validation exception with detailed error information
 */
class ValidationException extends \Exception {
    private array $validationErrors = [];
    
    /**
     * Constructor with validation errors
     */
    public function __construct(string $message, array $errors = []) {
        parent::__construct($message);
        $this->validationErrors = $errors;
    }
    
    /**
     * Get validation errors
     */
    public function getValidationErrors(): array {
        return $this->validationErrors;
    }
    
    /**
     * Get first error for a field
     */
    public function getFieldError(string $field): ?string {
        return $this->validationErrors[$field][0] ?? null;
    }
    
    /**
     * Check if field has errors
     */
    public function hasFieldError(string $field): bool {
        return isset($this->validationErrors[$field]);
    }
    
    /**
     * Get all error messages as flat array
     */
    public function getAllErrorMessages(): array {
        $messages = [];
        
        foreach ($this->validationErrors as $field => $errors) {
            foreach ($errors as $error) {
                $messages[] = "{$field}: {$error}";
            }
        }
        
        return $messages;
    }
}
