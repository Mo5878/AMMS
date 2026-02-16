<?php
// Enable error display for debugging (REMOVE in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| Railway Environment Variables
|--------------------------------------------------------------------------
| Railway automatically provides environment variables.
| Do NOT rely on .env file in production.
*/

define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASSWORD'));
define('DB_NAME', getenv('DB_NAME')); // usually "railway"
define('DB_PORT', getenv('DB_PORT') ?: 3306);

// Basic validation (very important)
if (!DB_HOST || !DB_USER || !DB_NAME) {
    die("Database environment variables are not set in Railway.");
}

// App Configuration
define('SESSION_TIMEOUT', 1800);
define('APP_NAME', 'Agri-Market Management System');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

date_default_timezone_set('UTC');

// Create PDO connection
try {
    $dsn = "mysql:host=" . DB_HOST .
           ";port=" . DB_PORT .
           ";dbname=" . DB_NAME .
           ";charset=utf8mb4";

    $conn = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5
    ]);

} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
}

$_SESSION['last_activity'] = time();
?>
