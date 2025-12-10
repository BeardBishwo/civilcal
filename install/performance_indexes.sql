-- Performance Indexes for Bishwo Calculator
-- Run via: php install/apply_indexes.php

-- Users table
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- Modules table  
CREATE INDEX idx_modules_name ON modules(name);
CREATE INDEX idx_modules_is_active ON modules(is_active);

-- Pages table
CREATE INDEX idx_pages_slug ON pages(slug);
CREATE INDEX idx_pages_status ON pages(status);

-- Settings table
CREATE INDEX idx_settings_key ON settings(`key`);
