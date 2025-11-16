<?php
namespace App\Services;

/**
 * Query optimization service
 * Analyzes and optimizes database queries for better performance
 */
class QueryOptimizer {
    private $database;
    private $logger;
    private array $queryCache = [];
    private array $queryStats = [];
    private bool $enabled = true;
    
    public function __construct($database, $logger = null) {
        $this->database = $database;
        $this->logger = $logger;
        $this->enabled = getenv('QUERY_OPTIMIZATION_ENABLED') !== 'false';
    }
    
    /**
     * Execute a query with optimization
     */
    public function executeOptimized(string $query, array $params = [], string $hint = ''): mixed {
        if (!$this->enabled) {
            return $this->database->query($query, $params);
        }
        
        $queryHash = md5($query . serialize($params));
        $startTime = microtime(true);
        
        try {
            // Check if query can be optimized
            $optimizedQuery = $this->analyzeAndOptimizeQuery($query, $params);
            
            // Execute the query
            $result = $this->database->query($optimizedQuery, $params);
            
            // Record statistics
            $executionTime = microtime(true) - $startTime;
            $this->recordQueryStats($queryHash, $query, $optimizedQuery, $executionTime, count($params));
            
            // Log slow queries
            if ($executionTime > 1.0) {
                $this->logSlowQuery($query, $optimizedQuery, $executionTime, $hint);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            $this->logQueryError($query, $e);
            throw $e;
        }
    }
    
    /**
     * Get query statistics
     */
    public function getQueryStats(): array {
        $stats = [];
        
        foreach ($this->queryStats as $hash => $data) {
            $totalTime = array_sum(array_column($data['executions'], 'execution_time'));
            $avgTime = $totalTime / count($data['executions']);
            $maxTime = max(array_column($data['executions'], 'execution_time'));
            $minTime = min(array_column($data['executions'], 'execution_time'));
            
            $stats[$hash] = [
                'original_query' => $data['original_query'],
                'optimized_query' => $data['optimized_query'],
                'execution_count' => count($data['executions']),
                'total_time' => $totalTime,
                'average_time' => $avgTime,
                'max_time' => $maxTime,
                'min_time' => $minTime,
                'last_execution' => end($data['executions'])['timestamp']
            ];
        }
        
        // Sort by total execution time
        usort($stats, function($a, $b) {
            return $b['total_time'] - $a['total_time'];
        });
        
        return $stats;
    }
    
    /**
     * Get slow queries
     */
    public function getSlowQueries(float $threshold = 0.5): array {
        $slowQueries = [];
        
        foreach ($this->queryStats as $hash => $data) {
            $avgTime = array_sum(array_column($data['executions'], 'execution_time')) / count($data['executions']);
            
            if ($avgTime > $threshold) {
                $slowQueries[$hash] = [
                    'query' => $data['original_query'],
                    'average_time' => $avgTime,
                    'execution_count' => count($data['executions']),
                    'total_time' => array_sum(array_column($data['executions'], 'execution_time'))
                ];
            }
        }
        
        return $slowQueries;
    }
    
    /**
     * Analyze and optimize a query
     */
    private function analyzeAndOptimizeQuery(string $query, array $params): string {
        $optimizedQuery = $query;
        
        // Remove extra whitespace
        $optimizedQuery = preg_replace('/\s+/', ' ', trim($optimizedQuery));
        
        // Analyze query type
        $queryType = $this->getQueryType($optimizedQuery);
        
        switch ($queryType) {
            case 'SELECT':
                $optimizedQuery = $this->optimizeSelectQuery($optimizedQuery, $params);
                break;
            case 'INSERT':
                $optimizedQuery = $this->optimizeInsertQuery($optimizedQuery, $params);
                break;
            case 'UPDATE':
                $optimizedQuery = $this->optimizeUpdateQuery($optimizedQuery, $params);
                break;
            case 'DELETE':
                $optimizedQuery = $this->optimizeDeleteQuery($optimizedQuery, $params);
                break;
        }
        
        return $optimizedQuery;
    }
    
    /**
     * Get query type
     */
    private function getQueryType(string $query): string {
        $query = strtoupper(trim($query));
        
        if (preg_match('/^\s*(WITH\s+[\s\S]*?)?(SELECT|INSERT|UPDATE|DELETE)/i', $query, $matches)) {
            return strtoupper($matches[2]);
        }
        
        return 'UNKNOWN';
    }
    
    /**
     * Optimize SELECT queries
     */
    private function optimizeSelectQuery(string $query, array $params): string {
        // Check for SELECT * and suggest specific columns if possible
        if (preg_match('/SELECT\s+\*/i', $query)) {
            // This is a warning optimization - we don't change the query but could log it
            $this->logInefficientQuery('SELECT * found - consider specifying columns', $query);
        }
        
        // Add LIMIT if no LIMIT clause and query looks expensive
        if (!preg_match('/LIMIT\s/i', $query) && 
            preg_match('/FROM\s+[\w_]+\s+WHERE/is', $query)) {
            // Only add LIMIT for certain types of queries to avoid breaking functionality
            if (getenv('AUTO_ADD_LIMIT') === 'true') {
                $query = preg_replace('/(\sORDER\s+BY\s+[\s\S]*?)?(;?)$/i', ' LIMIT 1000$1$2', $query);
            }
        }
        
        // Optimize JOINs
        $query = $this->optimizeJoins($query);
        
        return $query;
    }
    
    /**
     * Optimize INSERT queries
     */
    private function optimizeInsertQuery(string $query, array $params): string {
        // Batch multiple INSERTs if possible
        if (preg_match('/INSERT\s+INTO\s+(\w+)\s+/i', $query, $matches)) {
            $table = $matches[1];
            
            // Check if we can batch this with other inserts
            if (isset($this->queryCache[$table . '_batch'])) {
                $this->queryCache[$table . '_batch'][] = $query;
                
                // If we have enough queries to batch, create a batch insert
                if (count($this->queryCache[$table . '_batch']) >= 5) {
                    return $this->createBatchInsert($table, $this->queryCache[$table . '_batch']);
                }
            } else {
                $this->queryCache[$table . '_batch'] = [$query];
            }
        }
        
        return $query;
    }
    
    /**
     * Optimize UPDATE queries
     */
    private function optimizeUpdateQuery(string $query, array $params): string {
        // Add LIMIT to UPDATE queries without LIMIT (safety optimization)
        if (!preg_match('/LIMIT\s/i', $query) && 
            preg_match('/UPDATE\s+\w+\s+SET\s+\w+\s*=/i', $query)) {
            
            if (getenv('AUTO_ADD_UPDATE_LIMIT') === 'true') {
                $query = preg_replace('/(\sWHERE\s+[\s\S]*?)?(;?)$/i', '$1 LIMIT 1000$2', $query);
            }
        }
        
        return $query;
    }
    
    /**
     * Optimize DELETE queries
     */
    private function optimizeDeleteQuery(string $query, array $params): string {
        // Add LIMIT to DELETE queries without LIMIT (safety optimization)
        if (!preg_match('/LIMIT\s/i', $query) && 
            preg_match('/DELETE\s+FROM\s+\w+\s+WHERE/i', $query)) {
            
            if (getenv('AUTO_ADD_DELETE_LIMIT') === 'true') {
                $query = preg_replace('/(\sWHERE\s+[\s\S]*?)?(;?)$/i', '$1 LIMIT 1000$2', $query);
            }
        }
        
        return $query;
    }
    
    /**
     * Optimize JOINs in queries
     */
    private function optimizeJoins(string $query): string {
        // Look for potential JOIN optimizations
        if (preg_match_all('/JOIN\s+(\w+)\s+ON\s+([\s\S]*?)(?=\s+(?:JOIN|WHERE|GROUP|ORDER|LIMIT|$))/i', $query, $matches)) {
            foreach ($matches[1] as $table) {
                // Check if table has indexes that could be used
                $this->analyzeTableIndexes($table);
            }
        }
        
        return $query;
    }
    
    /**
     * Analyze table indexes
     */
    private function analyzeTableIndexes(string $table): void {
        try {
            // Get index information for the table
            $indexQuery = "SHOW INDEX FROM {$table}";
            $result = $this->database->query($indexQuery);
            $indexes = $result->fetchAll();
            
            // Log missing indexes that could improve performance
            if (empty($indexes)) {
                $this->logMissingIndex($table);
            }
            
        } catch (\Exception $e) {
            // Table might not exist or we don't have permission
            $this->logQueryError("Failed to analyze indexes for table {$table}", $e);
        }
    }
    
    /**
     * Create batch INSERT query
     */
    private function createBatchInsert(string $table, array $queries): string {
        if (count($queries) < 2) {
            return $queries[0];
        }
        
        // Extract values from individual INSERT queries
        $values = [];
        $columns = '';
        
        foreach ($queries as $query) {
            if (preg_match('/INSERT\s+INTO\s+' . preg_quote($table) . '\s*(\([^)]+\))?\s+VALUES\s*(\([^)]+\))/i', $query, $matches)) {
                if (empty($columns)) {
                    $columns = $matches[1] ?? '';
                }
                $values[] = $matches[2];
            }
        }
        
        if (!empty($values)) {
            // Create batch INSERT
            $batchQuery = "INSERT INTO {$table} {$columns} VALUES " . implode(', ', $values);
            
            // Clear the batch cache
            unset($this->queryCache[$table . '_batch']);
            
            return $batchQuery;
        }
        
        return $queries[0]; // Return first query if we can't batch
    }
    
    /**
     * Record query statistics
     */
    private function recordQueryStats(string $hash, string $original, string $optimized, float $executionTime, int $paramCount): void {
        if (!isset($this->queryStats[$hash])) {
            $this->queryStats[$hash] = [
                'original_query' => $original,
                'optimized_query' => $optimized,
                'executions' => []
            ];
        }
        
        $this->queryStats[$hash]['executions'][] = [
            'execution_time' => $executionTime,
            'param_count' => $paramCount,
            'timestamp' => microtime(true)
        ];
        
        // Keep only last 50 executions per query
        if (count($this->queryStats[$hash]['executions']) > 50) {
            array_shift($this->queryStats[$hash]['executions']);
        }
    }
    
    /**
     * Log slow query
     */
    private function logSlowQuery(string $query, string $optimized, float $executionTime, string $hint = ''): void {
        if ($this->logger) {
            $this->logger->warning("Slow Query Detected", [
                'original_query' => $query,
                'optimized_query' => $optimized,
                'execution_time' => $executionTime,
                'hint' => $hint
            ]);
        }
    }
    
    /**
     * Log inefficient query patterns
     */
    private function logInefficientQuery(string $message, string $query): void {
        if ($this->logger) {
            $this->logger->info("Inefficient Query Pattern", [
                'message' => $message,
                'query' => $query
            ]);
        }
    }
    
    /**
     * Log missing index
     */
    private function logMissingIndex(string $table): void {
        if ($this->logger) {
            $this->logger->warning("Missing Index Detected", [
                'table' => $table,
                'message' => "Table {$table} has no indexes, consider adding appropriate indexes for better performance"
            ]);
        }
    }
    
    /**
     * Log query error
     */
    private function logQueryError(string $query, \Exception $e): void {
        if ($this->logger) {
            $this->logger->error("Query Error", [
                'query' => $query,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }
    }
    
    /**
     * Get optimization recommendations
     */
    public function getOptimizationRecommendations(): array {
        $recommendations = [];
        
        // Check for slow queries
        $slowQueries = $this->getSlowQueries(0.1);
        foreach ($slowQueries as $hash => $data) {
            $recommendations[] = [
                'type' => 'slow_query',
                'priority' => $data['average_time'] > 1.0 ? 'high' : 'medium',
                'message' => "Query takes {$data['average_time']} seconds on average",
                'query' => $data['query'],
                'suggestion' => 'Consider adding indexes or optimizing the query structure'
            ];
        }
        
        // Check for inefficient patterns
        foreach ($this->queryStats as $data) {
            if (preg_match('/SELECT\s+\*/i', $data['original_query'])) {
                $recommendations[] = [
                    'type' => 'inefficient_select',
                    'priority' => 'low',
                    'message' => 'Using SELECT * instead of specific columns',
                    'query' => $data['original_query'],
                    'suggestion' => 'Specify only the columns you need'
                ];
            }
        }
        
        return $recommendations;
    }
    
    /**
     * Clear query statistics
     */
    public function clearStats(): void {
        $this->queryStats = [];
        $this->queryCache = [];
    }
    
    /**
     * Export statistics
     */
    public function exportStats(): string {
        return json_encode([
            'query_stats' => $this->queryStats,
            'optimization_recommendations' => $this->getOptimizationRecommendations(),
            'export_time' => microtime(true)
        ], JSON_PRETTY_PRINT);
    }
}
