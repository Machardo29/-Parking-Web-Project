<?php
// setup_database.php - Creates database, tables, and sample data for Car Parking System
$host = 'localhost';
$db   = 'car_parking';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Connect to MySQL server (no database yet)
try {
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    exit('MySQL connection failed: ' . $e->getMessage());
}
// Create database if not exists
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET $charset COLLATE utf8mb4_unicode_ci;");

// Now connect to the database
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

function tableExists($pdo, $table) {
    // MariaDB/XAMPP does not support parameter binding for table names
    $table = addslashes($table);
    $stmt = $pdo->query("SHOW TABLES LIKE '" . $table . "'");
    return $stmt->fetch() !== false;
}

// USERS TABLE
if (!tableExists($pdo, 'users')) {
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        full_name VARCHAR(100),
        phone VARCHAR(20),
        role ENUM('user','admin','owner') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");
    echo "Created 'users' table.<br>";
}

// PARKING SPACES TABLE
if (!tableExists($pdo, 'parking_spaces')) {
    $pdo->exec("CREATE TABLE parking_spaces (
        id INT AUTO_INCREMENT PRIMARY KEY,
        space_number VARCHAR(10) UNIQUE NOT NULL,
        vehicle_type ENUM('two-wheeler','four-wheeler') NOT NULL,
        hourly_rate DECIMAL(6,2) NOT NULL,
        status ENUM('available','reserved','occupied') DEFAULT 'available',
        image VARCHAR(255),
        location VARCHAR(100),
        owner_id INT NOT NULL,
        total_spaces INT NOT NULL DEFAULT 1,
        available_spaces INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (owner_id) REFERENCES users(id)
    ) ENGINE=InnoDB;");
    echo "Created 'parking_spaces' table.<br>";
} else {
    // Add owner_id column if it doesn't exist
    $col = $pdo->query("SHOW COLUMNS FROM parking_spaces LIKE 'owner_id'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE parking_spaces ADD COLUMN owner_id INT NOT NULL DEFAULT 1, ADD FOREIGN KEY (owner_id) REFERENCES users(id)");
        echo "Added 'owner_id' column to 'parking_spaces'.<br>";
    }
    // Add total_spaces column if it doesn't exist
    $col = $pdo->query("SHOW COLUMNS FROM parking_spaces LIKE 'total_spaces'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE parking_spaces ADD COLUMN total_spaces INT NOT NULL DEFAULT 1");
        echo "Added 'total_spaces' column to 'parking_spaces'.<br>";
    }
    // Add available_spaces column if it doesn't exist
    $col = $pdo->query("SHOW COLUMNS FROM parking_spaces LIKE 'available_spaces'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE parking_spaces ADD COLUMN available_spaces INT NOT NULL DEFAULT 1");
        echo "Added 'available_spaces' column to 'parking_spaces'.<br>";
    }
}

// RESERVATIONS TABLE
if (!tableExists($pdo, 'reservations')) {
    $pdo->exec("CREATE TABLE reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        space_id INT NOT NULL,
        license_plate VARCHAR(20) NOT NULL,
        vehicle_type ENUM('two-wheeler','four-wheeler') NOT NULL,
        start_time DATETIME NOT NULL,
        end_time DATETIME,
        total_cost DECIMAL(8,2),
        status ENUM('active','completed','cancelled') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (space_id) REFERENCES parking_spaces(id)
    ) ENGINE=InnoDB;");
    echo "Created 'reservations' table.<br>";
}

// Option to insert sample data
if (isset($_GET['sample'])) {
    // Insert sample users
    $pdo->exec("INSERT IGNORE INTO users (username, password, email, full_name, phone, role) VALUES
        ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin@school.com', 'Admin User', '1234567890', 'admin'),
        ('john', '" . password_hash('john123', PASSWORD_DEFAULT) . "', 'john@example.com', 'John Doe', '9876543210', 'user'),
        ('olivia', '" . password_hash('owner123', PASSWORD_DEFAULT) . "', 'olivia@carpark.com', 'Olivia Owner', '5551234567', 'owner')");
    // Insert sample parking spaces
    $pdo->exec("INSERT IGNORE INTO parking_spaces (space_number, vehicle_type, hourly_rate, status, location, total_spaces, available_spaces) VALUES
        ('A1', 'two-wheeler', 10, 'available', 'Block A', 5, 5),
        ('A2', 'four-wheeler', 20, 'available', 'Block A', 3, 3),
        ('B1', 'two-wheeler', 10, 'reserved', 'Block B', 2, 1),
        ('B2', 'four-wheeler', 20, 'occupied', 'Block B', 1, 0)");
    // Insert sample reservation
    $pdo->exec("INSERT IGNORE INTO reservations (user_id, space_id, license_plate, vehicle_type, start_time, end_time, total_cost, status) VALUES
        (2, 1, 'MH12AB1234', 'two-wheeler', NOW(), NULL, NULL, 'active')");
    echo "Sample data inserted.<br>";
}

echo "<br>Database setup complete.";

require_once __DIR__ . '/db.php';
?>
