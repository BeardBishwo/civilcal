ALTER TABLE email_threads 
ADD COLUMN calculator_url VARCHAR(255) NULL AFTER category,
ADD COLUMN screenshot_path VARCHAR(255) NULL AFTER calculator_url;
