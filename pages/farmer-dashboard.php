<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';
require_once '../includes/Order.php';

$auth = new Auth($conn);
$product = new Product($conn);
$order = new Order($conn);

// Check if user is logged in and is a farmer or admin
if (!$auth->isLoggedIn() || (!$auth->hasRole('farmer') && !$auth->hasRole('admin'))) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);
$farmer_products = $product->getFarmerProducts($user_id);
$farmer_orders = $order->getFarmerOrders($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Services Dashboard - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.php" class="logo">AMMS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="profile.php">My Profile</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
        <div class="dashboard-content">
            <div class="content">
            <h1>Farmer Services Dashboard</h1>
            
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="card-icon">➕</div>
                    <h3>Upload a New Product</h3>
                    <p>Add new products to your inventory</p>
                    <a href="farmer-product-edit.php">Upload Product</a>
                </div>

                <div class="stat-card">
                    <div class="card-icon">📈</div>
                    <h3>View  Trends</h3>
                    <p>Check current  trends</p>
                    <a href="farmer-market-prices.php">Market Analysis</a>
                </div>

                <div class="stat-card">
                    <div class="card-icon">📝</div>
                    <h3>Suggest </h3>
                    <p>Suggest fair prices for your categories</p>
                    <a href="farmer-set-market-price.php">Set Prices</a>
                </div>

                <div class="stat-card">
                    <div class="card-icon">📦</div>
                    <h3>Track Your Orders</h3>
                    <p class="stat-number"><?php echo count($farmer_orders); ?></p>
                    <a href="farmer-orders.php">Track Orders</a>
                </div>

                <div class="stat-card">
                    <div class="card-icon">📥</div>
                    <h3>Download Sales Report</h3>
                    <p>Download your sales and transaction reports</p>
                    <a href="farmer-reports.php">Download Report</a>
                </div>
            </div>

            <?php include_once __DIR__ . '/../includes/market_price_card.php'; ?>

            <div class="section">
                <h2>Profile Information</h2>
                <div class="profile-info">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>

            <div class="section">
                <h2>Recent Orders</h2>
                <?php if (count($farmer_orders) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Buyer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($farmer_orders, 0, 5) as $o): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($o['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($o['buyer_name']); ?></td>
                                    <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                    <td><span class="badge badge-<?php echo $o['order_status']; ?>"><?php echo ucfirst($o['order_status']); ?></span></td>
                                    <td><span class="badge badge-<?php echo $o['payment_status']; ?>"><?php echo ucfirst($o['payment_status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                    <td><a href="farmer-order-details.php?id=<?php echo $o['id']; ?>" class="btn-small">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No orders yet. Your products will appear here when buyers place orders.</p>
                <?php endif; ?>
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