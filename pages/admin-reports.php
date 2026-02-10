<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Admin.php';

// Check if user is logged in and is admin
if (!$auth->isLoggedIn() || !$auth->hasRole('admin')) {
    header("Location: login.php");
    exit();
}

$user = $auth->getUserById($auth->getUserId());
$sales_report = $admin->getSalesReport();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.php" class="logo">AMMS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="admin-dashboard.php">Dashboard</a></li>
                <li><a href="admin-users.php">Users</a></li>
                <li><a href="admin-products.php">Products</a></li>
                <li><a href="admin-orders.php">Orders</a></li>
                <li><a href="admin-reports.php">Reports</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <h1>System Reports</h1>

            <div class="section">
                <h2>Sales Report (Paid Orders Only)</h2>
                <p><strong>Total Revenue:</strong> $<?php echo number_format(array_sum(array_column($sales_report, 'total_amount')), 2); ?></p>
                <p><strong>Total Transactions:</strong> <?php echo count($sales_report); ?></p>

                <?php if (count($sales_report) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Buyer</th>
                                <th>Farmer</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales_report as $o): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($o['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($o['buyer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($o['farmer_name']); ?></td>
                                    <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No sales transactions found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>
</body>
</html>
