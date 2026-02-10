<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';

// Check if user is logged in and is a farmer
if (!$auth->isLoggedIn() || !$auth->hasRole('farmer')) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$product_id = $_GET['id'] ?? 0;
$p = $product->getProductById($product_id);

// Verify ownership
if (!$p || $p['farmer_id'] !== $user_id) {
    header("Location: farmer-products.php");
    exit();
}

// Delete product
$result = $product->deleteProduct($product_id, $user_id);

// Redirect back to products page
header("Location: farmer-products.php?deleted=1");
exit();
?>
