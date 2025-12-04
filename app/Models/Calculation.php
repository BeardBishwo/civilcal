<?php

namespace App\Models;

use App\Core\Database;
use Exception;

class Calculation
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a calculation by ID
     */
    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM calculation_history WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get all calculations with optional filters
     */
    public function getAll($filters = [], $page = 1, $perPage = 20)
    {
        $whereClause = "WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $whereClause .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['calculator_type'])) {
            $whereClause .= " AND calculator_type = ?";
            $params[] = $filters['calculator_type'];
        }

        if (!empty($filters['date_from'])) {
            $whereClause .= " AND created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereClause .= " AND created_at <= ?";
            $params[] = $filters['date_to'];
        }

        // Count total
        $countStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as total FROM calculation_history $whereClause");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        // Get calculations with pagination
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM calculation_history
            $whereClause
            ORDER BY created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $calculations = $stmt->fetchAll();

        return [
            'calculations' => $calculations,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Create a new calculation record
     */
    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO calculation_history (
                user_id,
                calculator_type,
                calculation_input,
                calculation_result,
                created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");

        $result = $stmt->execute([
            $data['user_id'] ?? null,
            $data['calculator_type'] ?? null,
            !empty($data['calculation_input']) ? json_encode($data['calculation_input']) : null,
            !empty($data['calculation_result']) ? json_encode($data['calculation_result']) : null
        ]);

        if ($result) {
            return $this->db->getPdo()->lastInsertId();
        }
        
        return false;
    }

    /**
     * Update an existing calculation
     */
    public function update($id, $data)
    {
        $updateFields = [];
        $values = [];

        if (isset($data['calculator_type'])) {
            $updateFields[] = 'calculator_type = ?';
            $values[] = $data['calculator_type'];
        }
        if (isset($data['calculation_input'])) {
            $updateFields[] = 'calculation_input = ?';
            $values[] = json_encode($data['calculation_input']);
        }
        if (isset($data['calculation_result'])) {
            $updateFields[] = 'calculation_result = ?';
            $values[] = json_encode($data['calculation_result']);
        }

        if (empty($updateFields)) {
            return false;
        }

        $updateFields[] = 'updated_at = NOW()';
        $values[] = $id;

        $sql = "UPDATE calculation_history SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);

        return $stmt->execute($values);
    }

    /**
     * Delete a calculation by ID
     */
    public function delete($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM calculation_history WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get calculation statistics
     */
    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total_calculations,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(*) as total_calculations_30_days
            FROM calculation_history
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        return $stmt->fetch();
    }

    /**
     * Get calculations by user
     */
    public function getByUser($userId, $limit = 50)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM calculation_history 
            WHERE user_id = ? 
            ORDER BY created_at DESC
            LIMIT $limit
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get calculations by calculator type
     */
    public function getByCalculatorType($calculatorType, $limit = 50)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM calculation_history 
            WHERE calculator_type = ? 
            ORDER BY created_at DESC
            LIMIT $limit
        ");
        $stmt->execute([$calculatorType]);
        return $stmt->fetchAll();
    }

    /**
     * Get recent calculations
     */
    public function getRecent($limit = 50)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM calculation_history 
            ORDER BY created_at DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get daily calculations for a date range
     */
    public function getDailyCalculations($days = 30)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM calculation_history
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    /**
     * Get calculator statistics
     */
    public function getCalculatorStats($limit = 10)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT calculator_type, COUNT(*) as usage_count
            FROM calculation_history
            GROUP BY calculator_type
            ORDER BY usage_count DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get calculator usage data
     */
    public function getCalculatorUsageData($days = 30)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT DATE(created_at) as date, calculator_type, COUNT(*) as count
            FROM calculation_history
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at), calculator_type
            ORDER BY date ASC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    /**
     * Get active user count for the last 30 days
     */
    public function getActiveUserCount($days = 30)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(DISTINCT user_id) as count
            FROM calculation_history
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $result = $stmt->fetch();
        return $result ? $result['count'] : 0;
    }

    /**
     * Get total calculation count
     */
    public function getTotalCalculationCount()
    {
        $stmt = $this->db->getPdo()->query("SELECT COUNT(*) as count FROM calculation_history");
        $result = $stmt->fetch();
        return $result ? $result['count'] : 0;
    }

    /**
     * Get monthly calculation count
     */
    public function getMonthlyCalculationCount($days = 30)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as count
            FROM calculation_history
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $result = $stmt->fetch();
        return $result ? $result['count'] : 0;
    }

    /**
     * Get calculation count between two relative day offsets
     */
    public function getCalculationCountBetween($startDays, $endDays = 0)
    {
        [$startDate, $endDate] = $this->buildDateRange($startDays, $endDays);

        $query = "SELECT COUNT(*) as count FROM calculation_history WHERE created_at >= :start";
        $params = ['start' => $startDate];

        if ($endDate) {
            $query .= " AND created_at < :end";
            $params['end'] = $endDate;
        }

        $stmt = $this->db->getPdo()->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get active user count for a relative day range
     */
    public function getActiveUserCountBetween($startDays, $endDays = 0)
    {
        [$startDate, $endDate] = $this->buildDateRange($startDays, $endDays);

        $query = "SELECT COUNT(DISTINCT user_id) as count FROM calculation_history WHERE created_at >= :start";
        $params = ['start' => $startDate];

        if ($endDate) {
            $query .= " AND created_at < :end";
            $params['end'] = $endDate;
        }

        $stmt = $this->db->getPdo()->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get most used calculators
     */
    public function getTopCalculators($limit = 5)
    {
        $pdo = $this->db->getPdo();

        $total = $this->getTotalCalculationCount();
        if ($total === 0) {
            return [];
        }

        $stmt = $pdo->prepare("
            SELECT calculator_type, COUNT(*) as usage
            FROM calculation_history
            GROUP BY calculator_type
            ORDER BY usage DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();

        $results = [];
        while ($row = $stmt->fetch()) {
            $usage = (int)$row['usage'];
            $results[] = [
                'name' => $row['calculator_type'] ?? 'Unknown',
                'uses' => $usage,
                'share' => $total > 0 ? round(($usage / $total) * 100, 1) : 0,
            ];
        }

        return $results;
    }

    /**
     * Helper to build absolute date range from relative days
     */
    private function buildDateRange($startDays, $endDays = 0)
    {
        $startDays = max(0, (int)$startDays);
        $endDays = max(0, (int)$endDays);

        $start = new \DateTime();
        $start->modify("-{$startDays} days");

        $end = null;
        if ($endDays > 0) {
            $end = new \DateTime();
            $end->modify("-{$endDays} days");
        }

        return [
            $start->format('Y-m-d H:i:s'),
            $end ? $end->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Get comprehensive calculation statistics
     */
    public function getCalculationStats()
    {
        // Get total count
        $totalStmt = $this->db->getPdo()->query("SELECT COUNT(*) as total FROM calculation_history");
        $total = $totalStmt->fetch()['total'] ?? 0;

        // Get unique users count
        $uniqueUsersStmt = $this->db->getPdo()->query("SELECT COUNT(DISTINCT user_id) as unique_users FROM calculation_history");
        $uniqueUsers = $uniqueUsersStmt->fetch()['unique_users'] ?? 0;

        // Get calculator types count
        $calculatorTypesStmt = $this->db->getPdo()->query("SELECT COUNT(DISTINCT calculator_type) as calculator_types FROM calculation_history");
        $calculatorTypes = $calculatorTypesStmt->fetch()['calculator_types'] ?? 0;

        // Get week count
        $weekStmt = $this->db->getPdo()->query("
            SELECT COUNT(*) as week_count
            FROM calculation_history
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $weekCount = $weekStmt->fetch()['week_count'] ?? 0;

        // Get month count
        $monthStmt = $this->db->getPdo()->query("
            SELECT COUNT(*) as month_count
            FROM calculation_history
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $monthCount = $monthStmt->fetch()['month_count'] ?? 0;

        return [
            'total' => $total,
            'unique_users' => $uniqueUsers,
            'calculator_types' => $calculatorTypes,
            'week_count' => $weekCount,
            'month_count' => $monthCount
        ];
    }

    /**
     * Get unique calculator types
     */
    public function getUniqueCalculatorTypes()
    {
        $stmt = $this->db->getPdo()->query("SELECT DISTINCT calculator_type FROM calculation_history");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Get database connection
     */
    public function getDb()
    {
        return $this->db;
    }
}