<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'secure_app';
    private $username = 'root';
    private $password = '';
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // Fallback to file-based storage if database is not available
            $this->pdo = null;
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function isConnected() {
        return $this->pdo !== null;
    }
    
    // Initialize database tables
    public function initializeTables() {
        if (!$this->isConnected()) return false;
        
        try {
            // Users table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    name VARCHAR(100),
                    email VARCHAR(100),
                    role VARCHAR(20) DEFAULT 'user',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
            
            // Products table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    price DECIMAL(10,2) NOT NULL,
                    stock INT DEFAULT 0,
                    description TEXT,
                    created_by INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES users(id)
                )
            ");
            
            // User activities table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS user_activities (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT,
                    activity_type VARCHAR(50),
                    description TEXT,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                )
            ");
            
            // Insert default users if they don't exist
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users");
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                $this->pdo->prepare("
                    INSERT INTO users (username, password, name, email, role) VALUES
                    ('admin', ?, 'Administrador', 'admin@example.com', 'admin'),
                    ('user', ?, 'Usuario Regular', 'user@example.com', 'user')
                ")->execute([
                    password_hash('admin123', PASSWORD_DEFAULT),
                    password_hash('user123', PASSWORD_DEFAULT)
                ]);
                
                // Insert sample products
                $this->pdo->exec("
                    INSERT INTO products (name, price, stock, description, created_by) VALUES
                    ('Laptop Dell XPS', 1299.99, 15, 'Laptop de alta gama para profesionales', 1),
                    ('iPhone 15 Pro', 999.99, 8, 'Smartphone última generación', 1),
                    ('Samsung 4K Monitor', 399.99, 12, 'Monitor 4K para trabajo y gaming', 1),
                    ('Mechanical Keyboard', 149.99, 25, 'Teclado mecánico para gaming', 1),
                    ('Wireless Mouse', 79.99, 30, 'Mouse inalámbrico ergonómico', 1)
                ");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Database initialization error: " . $e->getMessage());
            return false;
        }
    }
}

// File-based storage fallback
class FileStorage {
    private $dataDir = 'data';
    
    public function __construct() {
        if (!file_exists($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }
    
    public function save($file, $data) {
        $filepath = $this->dataDir . '/' . $file . '.json';
        return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }
    
    public function load($file) {
        $filepath = $this->dataDir . '/' . $file . '.json';
        if (!file_exists($filepath)) {
            return [];
        }
        $content = file_get_contents($filepath);
        return json_decode($content, true) ?: [];
    }
    
    public function append($file, $data) {
        $existing = $this->load($file);
        $existing[] = $data;
        return $this->save($file, $existing);
    }
}

// Initialize database and storage
$db = new Database();
$storage = new FileStorage();

// Initialize database tables
$db->initializeTables();
?>