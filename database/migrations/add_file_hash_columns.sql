-- Add file_hash to library_files
SET @exist := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'library_files' AND column_name = 'file_hash');
SET @sql := IF(@exist = 0, 'ALTER TABLE library_files ADD COLUMN file_hash CHAR(64) NOT NULL AFTER id, ADD INDEX (file_hash)', 'SELECT "Column file_hash already exists in library_files"');
PREPARE stmt FROM @sql;
EXECUTE stmt;

-- Add file_hash to bounty_submissions
SET @exist2 := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'bounty_submissions' AND column_name = 'file_hash');
SET @sql2 := IF(@exist2 = 0, 'ALTER TABLE bounty_submissions ADD COLUMN file_hash CHAR(64) NOT NULL AFTER id, ADD INDEX (file_hash)', 'SELECT "Column file_hash already exists in bounty_submissions"');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
