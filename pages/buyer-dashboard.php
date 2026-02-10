<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';
require_once '../includes/Order.php';

// Check if user is logged in and is a buyer
if (!$auth->isLoggedIn() || !$auth->hasRole('buyer')) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);
$buyer_orders = $order->getBuyerOrders($user_id);

// Get products for browsing
$all_products = $product->getAllProducts(100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - AMMS</title>
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
        <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>

        <div class="dashboard-content">
            <div class="dashboard">
                <h1>Buyer Dashboard</h1>

                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="card-icon">🧾</div>
                        <h3>Total Orders</h3>
                        <p class="stat-number"><?php echo count($buyer_orders); ?></p>
                        <a href="buyer-orders.php">View Orders</a>
                    </div>

                    <div class="stat-card">
                        <div class="card-icon">⏳</div>
                        <h3>Pending Orders</h3>
                        <p class="stat-number"><?php echo count(array_filter($buyer_orders, fn($o) => $o['order_status'] === 'pending')); ?></p>
                        <a href="buyer-orders.php">Check Status</a>
                    </div>

                    <div class="stat-card">
                        <div class="card-icon">🛒</div>
                        <h3>Available Products</h3>
                        <p class="stat-number"><?php echo count($all_products); ?></p>
                        <a href="buyer-browse.php">Browse Now</a>
                    </div>

                    <div class="stat-card">
                        <div class="card-icon">💰</div>
                        <h3>Total Spent</h3>
                        <p class="stat-number">$<?php echo number_format(array_sum(array_column($buyer_orders, 'total_amount')), 2); ?></p>
                        <a href="buyer-orders.php">Order History</a>
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
                    <?php if (count($buyer_orders) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Farmer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($buyer_orders, 0, 5) as $o): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($o['order_number']); ?></td>
                                        <td><?php echo htmlspecialchars($o['farmer_name']); ?></td>
                                        <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                        <td><span class="badge badge-<?php echo $o['order_status']; ?>"><?php echo ucfirst($o['order_status']); ?></span></td>
                                        <td><span class="badge badge-<?php echo $o['payment_status']; ?>"><?php echo ucfirst($o['payment_status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                        <td><a href="buyer-order-details.php?id=<?php echo $o['id']; ?>" class="btn-small">View</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No orders yet. <a href="buyer-browse.php">Start browsing products</a> to place your first order!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>
</body>
</html>
