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
$all_products = $admin->getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - AMMS</title>
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
            <h1>Product Management</h1>

            <div class="section">
                <p><strong>Total Products:</strong> <?php echo count($all_products); ?></p>

                <?php if (count($all_products) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Farmer</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_products as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['farmer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['category']); ?></td>
                                    <td>$<?php echo number_format($p['price'], 2); ?></td>
                                    <td><?php echo number_format($p['quantity'], 2); ?> <?php echo htmlspecialchars($p['unit']); ?></td>
                                    <td><span class="badge badge-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($p['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>
</body>
</html>
