<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Order.php';

// Check if user is logged in and is a farmer
if (!$auth->isLoggedIn() || !$auth->hasRole('farmer')) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);
$farmer_orders = $order->getFarmerOrders($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.php" class="logo">AMMS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="farmer-dashboard.php">Dashboard</a></li>
                <li><a href="farmer-products.php">My Products</a></li>
                <li><a href="farmer-orders.php">Orders</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <h1>Incoming Orders</h1>

            <?php if (count($farmer_orders) > 0): ?>
                <div class="section">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Buyer</th>
                                <th>Amount</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($farmer_orders as $o): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($o['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($o['buyer_name']); ?></td>
                                    <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                    <td><span class="badge badge-<?php echo $o['order_status']; ?>"><?php echo ucfirst($o['order_status']); ?></span></td>
                                    <td><span class="badge badge-<?php echo $o['payment_status']; ?>"><?php echo ucfirst($o['payment_status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                    <td><a href="buyer-order-details.php?id=<?php echo $o['id']; ?>" class="btn-small">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="section">
                    <p>You don't have any incoming orders yet. Once buyers place orders for your products, they will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>
</body>
</html>
