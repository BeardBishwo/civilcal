<?php
namespace App\Core;

use App\Core\Exceptions\ValidationException;

/**
 * Enhanced base model with validation, security, and standardization
 * Provides consistent patterns across all models in the application
 * Maintains compatibility with parent Model class
 */
abstract class SafeModel extends Model {
    protected array $validationRules = [];
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $timestamps = ['created_at', 'updated_at'];
    
    /**
     * Create a new record with validation and security
     * Maintains compatibility with parent Model::create() method signature
     */
    public function create($data) {
        // First validate the data
        $this->validate($data);
        
        // Filter fillable fields and add timestamps
        $filteredData = $this->prepareDataForSave($data);
        
        // Call parent create method
        return parent::create($filteredData);
    }
    
    /**
     * Update a record with validation and security
     * Maintains compatibility with parent Model::update() method signature
     */
    public function update($id, $data) {
        // First validate the data
        $this->validate($data);
        
        // Filter fillable fields and add updated timestamp
        $filteredData = $this->prepareDataForUpdate($data);
        
        // Call parent update method
        return parent::update($id, $filteredData);
    }
    
    /**
     * Delete a record with soft delete support
     * Maintains compatibility with parent Model::delete() method signature
     */
    public function delete($id) {
        // Check if soft delete is enabled
        if (in_array('deleted_at', $this->fillable)) {
            return $this->softDelete($id);
        }
        
        // Hard delete - call parent delete method
        return parent::delete($id);
    }
    
    /**
     * Enhanced create method that returns detailed response
     */
    public function createWithResponse(array $data): array {
        try {
            // Validate the data
            $this->validate($data);
            
            // Filter fillable fields and add timestamps
            $filteredData = $this->prepareDataForSave($data);
            
            // Call parent create method
            $result = parent::create($filteredData);
            
            if ($result) {
                $insertedId = $this->db->lastInsertId() ?? null;
                return [
                    'success' => true, 
                    'id' => $insertedId,
                    'message' => 'Record created successfully'
                ];
            } else {
                return [
                    'success' => false, 
                    'error' => 'Failed to create record',
                    'message' => 'Database insertion failed'
                ];
            }
            
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'error' => 'validation_failed',
                'message' => $e->getMessage(),
                'errors' => $e->getValidationErrors()
            ];
        } catch (\PDOException $e) {
            error_log("Database error in " . static::class . "::createWithResponse: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'database_error',
                'message' => 'Database operation failed'
            ];
        } catch (\Exception $e) {
            error_log("Unexpected error in " . static::class . "::createWithResponse: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'unexpected_error',
                'message' => 'An unexpected error occurred'
            ];
        }
    }
    
    /**
     * Enhanced update method that returns detailed response
     */
    public function updateWithResponse(int $id, array $data): array {
        try {
            // Validate the data
            $this->validate($data);
            
            // Filter fillable fields and add updated timestamp
            $filteredData = $this->prepareDataForUpdate($data);
            
            // Call parent update method
            $result = parent::update($id, $filteredData);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Record updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to update record',
                    'message' => 'No rows were affected'
                ];
            }
            
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'error' => 'validation_failed',
                'message' => $e->getMessage(),
                'errors' => $e->getValidationErrors()
            ];
        } catch (\PDOException $e) {
            error_log("Database error in " . static::class . "::updateWithResponse: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'database_error',
                'message' => 'Database operation failed'
            ];
        } catch (\Exception $e) {
            error_log("Unexpected error in " . static::class . "::updateWithResponse: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'unexpected_error',
                'message' => 'An unexpected error occurred'
            ];
        }
    }
    
    /**
     * Enhanced delete method that returns detailed response
     */
    public function deleteWithResponse(int $id): array {
        try {
            // Check if soft delete is enabled
            if (in_array('deleted_at', $this->fillable)) {
                $result = $this->softDelete($id);
            } else {
                // Hard delete
                $result = parent::delete($id);
            }
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Record deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to delete record',
                    'message' => 'No rows were affected'
                ];
            }
            
        } catch (\PDOException $e) {
            error_log("Database error in " . static::class . "::deleteWithResponse: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'database_error',
                'message' => 'Database operation failed'
            ];
        } catch (\Exception $e) {
            error_log("Unexpected error in " . static::class . "::deleteWithResponse: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'unexpected_error',
                'message' => 'An unexpected error occurred'
            ];
        }
    }
    
    /**
     * Find record by field with validation
     */
    public function findBy(string $field, mixed $value): ?array {
        try {
            // Validate field name to prevent SQL injection
            if (!$this->isValidFieldName($field)) {
                throw new \InvalidArgumentException("Invalid field name: {$field}");
            }
            
            $conditions = [$field => $value];
            $results = $this->where($conditions);
            
            return !empty($results) ? $results[0] : null;
            
        } catch (\PDOException $e) {
            error_log("Database error in " . static::class . "::findBy: " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            error_log("Error in " . static::class . "::findBy: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search records with pagination
     */
    public function search(array $filters = [], int $page = 1, int $limit = 20): array {
        try {
            $offset = ($page - 1) * $limit;
            
            // Build WHERE conditions
            $conditions = [];
            $params = [];
            
            foreach ($filters as $field => $value) {
                if ($this->isValidFieldName($field) && !empty($value)) {
                    $conditions[] = "{$field} = ?";
                    $params[] = $value;
                }
            }
            
            $whereClause = !empty($conditions) ? ' WHERE ' . implode(' AND', $conditions) : '';
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM {$this->table}{$whereClause}";
            $stmt = $this->db->prepare($countQuery);
            $stmt->execute($params);
            $total = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Get paginated results
            $query = "SELECT * FROM {$this->table}{$whereClause} LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array_merge($params, [$limit, $offset]));
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => $results,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit),
                    'has_more' => ($page * $limit) < $total
                ]
            ];
            
        } catch (\PDOException $e) {
            error_log("Database error in " . static::class . "::search: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'database_error',
                'message' => 'Search operation failed'
            ];
        } catch (\Exception $e) {
            error_log("Error in " . static::class . "::search: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'unexpected_error',
                'message' => 'Search operation failed'
            ];
        }
    }
    
    /**
     * Filter fillable fields and prepare data for saving
     */
    private function prepareDataForSave(array $data): array {
        // Filter to only allow fillable fields
        $filteredData = $this->filterFillableFields($data);
        
        // Add timestamps
        foreach ($this->timestamps as $timestamp) {
            if (in_array($timestamp, $this->fillable)) {
                if ($timestamp === 'created_at' || $timestamp === 'updated_at') {
                    $filteredData[$timestamp] = date('Y-m-d H:i:s');
                }
            }
        }
        
        return $filteredData;
    }
    
    /**
     * Filter fillable fields and prepare data for updating
     */
    private function prepareDataForUpdate(array $data): array {
        // Filter to only allow fillable fields
        $filteredData = $this->filterFillableFields($data);
        
        // Add updated timestamp
        if (in_array('updated_at', $this->fillable)) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $filteredData;
    }
    
    /**
     * Filter fillable fields only
     */
    private function filterFillableFields(array $data): array {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Validate data against rules
     */
    private function validate(array $data): void {
        $errors = [];
        
        foreach ($this->validationRules as $field => $rules) {
            if (isset($data[$field])) {
                foreach ($rules as $rule) {
                    try {
                        $this->applyValidationRule($field, $data[$field], $rule);
                    } catch (ValidationException $e) {
                        $errors[$field][] = $e->getMessage();
                    }
                }
            } elseif (in_array('required', $rules)) {
                $errors[$field][] = "{$field} is required";
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
    }
    
    /**
     * Apply individual validation rule
     */
    private function applyValidationRule(string $field, mixed $value, string $rule): void {
        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== 0 && $value !== '0' && $value !== '') {
                    throw new ValidationException("{$field} is required");
                }
                break;
                
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException("{$field} must be a valid email address");
                }
                break;
                
            case 'numeric':
                if (!is_numeric($value)) {
                    throw new ValidationException("{$field} must be numeric");
                }
                break;
                
            case 'string':
                if (!is_string($value)) {
                    throw new ValidationException("{$field} must be a string");
                }
                break;
                
            case 'boolean':
                if (!is_bool($value) && !in_array($value, [0, 1, '0', '1'])) {
                    throw new ValidationException("{$field} must be a boolean value");
                }
                break;
                
            default:
                // Custom validation rule
                $methodName = 'validate' . ucfirst(str_replace('-', '', $rule));
                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value);
                }
                break;
        }
    }
    
    /**
     * Soft delete implementation
     */
    private function softDelete(int $id): bool {
        $data = ['deleted_at' => date('Y-m-d H:i:s')];
        
        return parent::update($id, $data);
    }
    
    /**
     * Validate field name to prevent SQL injection
     */
    private function isValidFieldName(string $field): bool {
        // Allow only alphanumeric characters and underscores
        return preg_match('/^[a-zA-Z0-9_]+$/', $field) === 1;
    }
}
