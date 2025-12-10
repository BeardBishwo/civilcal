-- Performance Indexes for Production
-- Run this to optimize database queries

-- Users Table
ALTER TABLE `users` ADD INDEX `idx_users_email` (`email`);
ALTER TABLE `users` ADD INDEX `idx_users_role` (`role`);

-- Modules Table
ALTER TABLE `modules` ADD INDEX `idx_modules_slug` (`slug`);
ALTER TABLE `modules` ADD INDEX `idx_modules_status` (`status`);

-- Pages Table
ALTER TABLE `pages` ADD INDEX `idx_pages_slug` (`slug`);
ALTER TABLE `pages` ADD INDEX `idx_pages_status` (`status`);

-- Media Table
ALTER TABLE `media` ADD INDEX `idx_media_type` (`file_type`);
