<?php
// Update these values if your XAMPP MySQL settings are different.
$host = "localhost";
$dbname = "smart_city_grievance";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit("Database connection failed. Check config.php and make sure MySQL is running. " . htmlspecialchars($e->getMessage()));
}
