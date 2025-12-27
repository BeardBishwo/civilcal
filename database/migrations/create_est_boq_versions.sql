CREATE TABLE IF NOT EXISTS est_boq_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    grid_data LONGTEXT NOT NULL,
    changed_by INT,
    change_description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project_id (project_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (project_id) REFERENCES est_projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
