-- =====================================================
-- Image Optimization - Database Schema Update
-- =====================================================

-- Add optimization columns to media table
ALTER TABLE media 
ADD COLUMN optimized TINYINT(1) DEFAULT 0,
ADD COLUMN original_size BIGINT NULL,
ADD COLUMN optimized_size BIGINT NULL,
ADD COLUMN compression_ratio DECIMAL(5,2) NULL,
ADD COLUMN has_webp TINYINT(1) DEFAULT 0,
ADD COLUMN thumbnail_path VARCHAR(255) NULL,
ADD COLUMN medium_path VARCHAR(255) NULL;

-- Update existing records to show unoptimized status
UPDATE media SET optimized = 0 WHERE optimized IS NULL;
