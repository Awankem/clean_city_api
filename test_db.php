<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'clean_city';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    echo "Connected to MySQL server successfully.\n";
    
    $stmt = $pdo->query("SHOW DATABASES LIKE '$db'");
    if ($stmt->rowCount() > 0) {
        echo "Database '$db' exists.\n";
    } else {
        echo "Database '$db' does NOT exist. Attempting to create...\n";
        $pdo->exec("CREATE DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "Database '$db' created successfully.\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    
    echo "Trying with localhost...\n";
    try {
        $pdo = new PDO("mysql:host=localhost", $user, $pass);
        echo "Connected to MySQL server using 'localhost' successfully.\n";
    } catch (PDOException $e2) {
        echo "Connection to 'localhost' also failed: " . $e2->getMessage() . "\n";
    }
}
