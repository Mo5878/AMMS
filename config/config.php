<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Adjust path to autoload.php

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database Configuration using environment variables
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'amms');

// Session Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('APP_NAME', 'Agri-Market Management System');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost/amms');

// Site Configuration
define('SITE_TIMEZONE', 'UTC');
date_default_timezone_set(SITE_TIMEZONE);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check session timeout
if (isset($_SESSION['last_activity'])) {
    if ((time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
    }
}
$_SESSION['last_activity'] = time();
?>
