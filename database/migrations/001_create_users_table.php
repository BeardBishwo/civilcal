class CreateUsersTable {
    public function up() {
        $pdo = new PDO("mysql:host=localhost;dbname=bishwo_calculator", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            company VARCHAR(255),
            profession VARCHAR(100),
            role ENUM('user', 'admin') DEFAULT 'user',
            subscription_id INT DEFAULT 1,
            subscription_status ENUM('active', 'canceled', 'expired') DEFAULT 'active',
            subscription_ends_at TIMESTAMP NULL,
            email_verified_at TIMESTAMP NULL,
            remember_token VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "✅ Created users table\n";
    }
    
    public function down() {
        // Add down method if needed
    }
}
