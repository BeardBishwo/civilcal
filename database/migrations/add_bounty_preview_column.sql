-- Add preview_path column to bounty_submissions
ALTER TABLE bounty_submissions ADD COLUMN preview_path VARCHAR(255) AFTER file_path;
