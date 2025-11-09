<?php

class CreateCalculationHistoryTable
{
    public function up($pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS calculation_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            calculator_type VARCHAR(100) NOT NULL,
            calculation_title VARCHAR(255) NOT NULL,
            input_data JSON NOT NULL,
            result_data JSON NOT NULL,
            calculation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_favorite BOOLEAN DEFAULT FALSE,
            tags VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_date (user_id, calculation_date),
            INDEX idx_calculator_type (calculator_type),
            INDEX idx_favorite (is_favorite),
            INDEX idx_calculation_date (calculation_date)
        ) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
    }

    public function down($pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS calculation_history");
    }
}
