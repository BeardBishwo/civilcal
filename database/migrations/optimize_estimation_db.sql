-- =====================================================
-- DATABASE OPTIMIZATION FOR ESTIMATION MODULE
-- =====================================================

-- 1. ADD INDEXES FOR FREQUENTLY QUERIED COLUMNS
-- =====================================================

-- est_projects: Optimize project lookups
ALTER TABLE est_projects 
ADD INDEX idx_name (name),
ADD INDEX idx_created_at (created_at),
ADD INDEX idx_location_id (location_id);

-- est_boq_data: Optimize BOQ data retrieval
ALTER TABLE est_boq_data 
ADD INDEX idx_project_id (project_id),
ADD INDEX idx_updated_at (updated_at);

-- est_item_master: Optimize item searches
ALTER TABLE est_item_master 
ADD INDEX idx_code (code),
ADD INDEX idx_category (category),
ADD INDEX idx_name (name(100));

-- est_locations: Optimize location searches
ALTER TABLE est_locations 
ADD INDEX idx_type (type),
ADD INDEX idx_parent_id (parent_id),
ADD INDEX idx_name_en (name_en(100)),
ADD INDEX idx_name_np (name_np(100));

-- est_local_rates: Optimize rate lookups
ALTER TABLE est_local_rates 
ADD INDEX idx_item_location (item_code, location_id),
ADD INDEX idx_location_id (location_id),
ADD INDEX idx_updated_at (updated_at);

-- est_templates: Optimize template searches
ALTER TABLE est_templates 
ADD INDEX idx_created_by (created_by),
ADD INDEX idx_name (name(100)),
ADD INDEX idx_created_at (created_at);

-- est_boq_versions: Optimize version history
ALTER TABLE est_boq_versions 
ADD INDEX idx_project_created (project_id, created_at DESC),
ADD INDEX idx_created_at (created_at DESC);

-- 2. OPTIMIZE TABLE STORAGE
-- =====================================================

-- Analyze tables to update statistics
ANALYZE TABLE est_projects;
ANALYZE TABLE est_boq_data;
ANALYZE TABLE est_item_master;
ANALYZE TABLE est_locations;
ANALYZE TABLE est_local_rates;
ANALYZE TABLE est_templates;
ANALYZE TABLE est_boq_versions;

-- Optimize tables to reclaim space
OPTIMIZE TABLE est_projects;
OPTIMIZE TABLE est_boq_data;
OPTIMIZE TABLE est_item_master;
OPTIMIZE TABLE est_locations;
OPTIMIZE TABLE est_local_rates;
OPTIMIZE TABLE est_templates;
OPTIMIZE TABLE est_boq_versions;

-- 3. ADD COMPOSITE INDEXES FOR COMPLEX QUERIES
-- =====================================================

-- Optimize version history queries (project + date)
ALTER TABLE est_boq_versions 
ADD INDEX idx_project_date (project_id, created_at DESC);

-- Optimize rate lookups by location and item
ALTER TABLE est_local_rates 
ADD INDEX idx_location_item (location_id, item_code);

-- 4. CLEANUP OLD VERSION HISTORY (Keep last 50 per project)
-- =====================================================

CREATE PROCEDURE cleanup_old_versions()
BEGIN
    DELETE v1 FROM est_boq_versions v1
    WHERE v1.id NOT IN (
        SELECT id FROM (
            SELECT id 
            FROM est_boq_versions v2
            WHERE v2.project_id = v1.project_id
            ORDER BY created_at DESC
            LIMIT 50
        ) AS keep_versions
    );
END;

-- 5. CREATE BACKUP PROCEDURE
-- =====================================================

CREATE PROCEDURE backup_estimation_data()
BEGIN
    -- Create backup tables with timestamp
    SET @backup_suffix = DATE_FORMAT(NOW(), '%Y%m%d_%H%i%s');
    
    SET @sql = CONCAT('CREATE TABLE est_projects_backup_', @backup_suffix, ' AS SELECT * FROM est_projects');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SET @sql = CONCAT('CREATE TABLE est_boq_data_backup_', @backup_suffix, ' AS SELECT * FROM est_boq_data');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END;

-- 6. PERFORMANCE MONITORING VIEWS
-- =====================================================

-- View to check table sizes
CREATE OR REPLACE VIEW v_estimation_table_sizes AS
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
    table_rows,
    ROUND((index_length / 1024 / 1024), 2) AS index_size_mb
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
AND table_name LIKE 'est_%'
ORDER BY (data_length + index_length) DESC;

-- View to check index usage
CREATE OR REPLACE VIEW v_estimation_indexes AS
SELECT 
    table_name,
    index_name,
    non_unique,
    seq_in_index,
    column_name,
    cardinality
FROM information_schema.STATISTICS
WHERE table_schema = DATABASE()
AND table_name LIKE 'est_%'
ORDER BY table_name, index_name, seq_in_index;

-- 7. QUERY OPTIMIZATION HINTS
-- =====================================================

-- Example optimized queries for common operations:

-- Get project with location (uses indexes)
-- SELECT p.*, l.name_en as location_name 
-- FROM est_projects p 
-- LEFT JOIN est_locations l ON p.location_id = l.id 
-- WHERE p.id = ? 
-- LIMIT 1;

-- Get rates for location (uses composite index)
-- SELECT item_code, rate 
-- FROM est_local_rates 
-- WHERE location_id = ? 
-- ORDER BY item_code;

-- Get recent versions (uses project_date index)
-- SELECT * 
-- FROM est_boq_versions 
-- WHERE project_id = ? 
-- ORDER BY created_at DESC 
-- LIMIT 20;

-- 8. MAINTENANCE SCHEDULE
-- =====================================================

-- Run weekly (cleanup old versions beyond 50)
-- CALL cleanup_old_versions();

-- Run monthly (optimize tables)
-- OPTIMIZE TABLE est_projects, est_boq_data, est_item_master, 
--                est_locations, est_local_rates, est_templates, est_boq_versions;

-- Run before major updates (backup)
-- CALL backup_estimation_data();
