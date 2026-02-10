<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';

// Check if user is logged in and is a buyer or admin
if (!$auth->isLoggedIn() || (!$auth->hasRole('buyer') && !$auth->hasRole('admin'))) {
    header("Location: login.php");
    exit();
}

$product_id = $_GET['id'] ?? 0;
$p = $product->getProductById($product_id);

if (!$p) {
    header("Location: buyer-browse.php");
    exit();
}

$user = $auth->getUserById($auth->getUserId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['name']); ?> - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.php" class="logo">AMMS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="buyer-dashboard.php">Dashboard</a></li>
                <li><a href="buyer-browse.php">Browse Products</a></li>
                <li><a href="buyer-orders.php">My Orders</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <li class="sidebar-item"><a href="buyer-dashboard.php">Dashboard</a></li>
                <li class="sidebar-item"><a href="buyer-browse.php" class="active">Browse Products</a></li>
                <li class="sidebar-item"><a href="buyer-orders.php">My Orders</a></li>
                <li class="sidebar-item"><a href="profile.php">Profile</a></li>
            </nav>
        </aside>
        <div class="dashboard-content">
            <div class="content">
            <a href="buyer-browse.php" class="btn btn-secondary" style="margin-bottom: 20px;">← Back to Products</a>

            <div class="product-detail">
                <h1><?php echo htmlspecialchars($p['name']); ?></h1>
                
                <div class="detail-grid">
                    <div class="detail-main">
                        <div class="info-box">
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($p['category']); ?></p>
                            <p><strong>Farmer:</strong> <?php echo htmlspecialchars($p['farmer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($p['farmer_email']); ?></p>
                            <?php if ($p['description']): ?>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($p['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="detail-sidebar">
                        <div class="price-box">
                            <p class="price">$<?php echo number_format($p['price'], 2); ?>/<?php echo htmlspecialchars($p['unit']); ?></p>
                            <p><strong>Available:</strong> <?php echo number_format($p['quantity'], 2); ?> <?php echo htmlspecialchars($p['unit']); ?></p>
                            <p><strong>Status:</strong> <span class="badge badge-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></p>
                            
                            <div style="margin-top: 20px;">
                                <a href="buyer-order-create.php?product_id=<?php echo $p['id']; ?>" class="btn btn-primary" style="width: 100%;">Place Order</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p><strong>Agri-Market Management System (AMMS)</strong></p>
            <p>Contact: +225 758 782 657 | Email: amosmayala14@gmail.com</p>
            <div class="footer-divider">
                &copy; 2026 Agri-Market Management System. All rights reserved. | Empowering farmers, serving communities, growing together.
            </div>
        </div>
    </footer>
</body>
</html>
