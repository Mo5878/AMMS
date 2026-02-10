<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Order.php';

// Check if user is logged in and is a farmer or admin
if (!$auth->isLoggedIn() || (!$auth->hasRole('farmer') && !$auth->hasRole('admin'))) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$o = $order->getOrderById($_GET['id'] ?? 0);

// Verify ownership
if (!$o || $o['farmer_id'] !== $user_id) {
    header("Location: farmer-orders.php");
    exit();
}

$user = $auth->getUserById($user_id);
$order_items = $order->getOrderItems($o['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - AMMS</title>
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
            <a href="farmer-orders.php" class="btn btn-secondary" style="margin-bottom: 20px;">← Back to Orders</a>

            <h1>Order Details</h1>

            <div class="order-details">
                <div class="detail-section">
                    <h2>Order Information</h2>
                    <div class="info-grid">
                        <div>
                            <p><strong>Order Number:</strong> <?php echo htmlspecialchars($o['order_number']); ?></p>
                            <p><strong>Order Date:</strong> <?php echo date('F d, Y H:i', strtotime($o['created_at'])); ?></p>
                        </div>
                        <div>
                            <p><strong>Order Status:</strong> <span class="badge badge-<?php echo $o['order_status']; ?>"><?php echo ucfirst($o['order_status']); ?></span></p>
                            <p><strong>Payment Status:</strong> <span class="badge badge-<?php echo $o['payment_status']; ?>"><?php echo ucfirst($o['payment_status']); ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h2>Buyer Information</h2>
                    <div class="info-box">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($o['buyer_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($o['buyer_email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($o['buyer_phone']); ?></p>
                    </div>
                </div>

                <div class="detail-section">
                    <h2>Order Items</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    <td><?php echo number_format($item['quantity'], 2); ?></td>
                                    <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                    <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="detail-section">
                    <h2>Order Summary</h2>
                    <div class="summary-box">
                        <p><strong>Total Amount:</strong> $<?php echo number_format($o['total_amount'], 2); ?></p>
                    </div>
                </div>

                <div class="detail-section">
                    <h2>Manage Order</h2>
                    <form method="POST" action="buyer-order-details.php?id=<?php echo $o['id']; ?>" class="form-row">
                        <div class="form-group">
                            <label for="order_status">Update Order Status</label>
                            <select id="order_status" name="order_status">
                                <option value="pending" <?php echo $o['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $o['order_status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="shipped" <?php echo $o['order_status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $o['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $o['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_order_status" value="1" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>

                    <form method="POST" action="buyer-order-details.php?id=<?php echo $o['id']; ?>" class="form-row" style="margin-top: 20px;">
                        <div class="form-group">
                            <label for="payment_status">Update Payment Status</label>
                            <select id="payment_status" name="payment_status">
                                <option value="pending" <?php echo $o['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="paid" <?php echo $o['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="cancelled" <?php echo $o['payment_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_payment_status" value="1" class="btn btn-primary">Update Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>
</body>
</html>
