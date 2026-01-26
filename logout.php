<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Destroy session and redirect
$auth->logout();
header("Location: ../index.php");
exit();
?>
