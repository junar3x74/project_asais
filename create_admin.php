<?php

require_once './configs/db.php'; 


require_once './vendor/autoload.php';

use Dotenv\Dotenv;


try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    die("Error loading .env file: " . $e->getMessage());
}


$adminEmail = $_ENV['ADMIN_EMAIL'] ?? '';
$adminPassword = $_ENV['ADMIN_PASSWORD'] ?? '';

if (empty($adminEmail) || empty($adminPassword)) {
    die("Error: Admin email or password not set in .env file.");
}

try {
    
    $deleteQuery = "DELETE FROM users WHERE role = 'admin'";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->execute();

    
    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

    
    $adminRole = 'admin';

    
    $insertQuery = "INSERT INTO users (fname, email, password, role, created_at) 
                    VALUES ('Admin', :email, :password, :role, NOW())";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->execute([
        'email' => $adminEmail,
        'password' => $hashedPassword,
        'role' => $adminRole
    ]);

    echo "Admin user has been created successfully with the 'admin' role.";
} catch (PDOException $e) {
    die("Error inserting admin user: " . $e->getMessage());
}
