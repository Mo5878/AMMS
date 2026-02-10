<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Admin.php';
require_once '../includes/DashboardHelpers.php';

$auth  = new Auth($conn);
$admin = new Admin($conn);

/* SECURITY CHECK */
if (!$auth->isLoggedIn() || !$auth->hasRole('admin')) {
    header("Location: login.php");
    exit();
}

$user        = $auth->getUserById($auth->getUserId());
$stats       = $admin->getStatistics();
$all_users   = $admin->getAllUsers();
$all_orders  = $admin->getAllOrders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | AMMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
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

<!-- ================= DASHBOARD WRAPPER ================= -->
<div class="dashboard-wrapper">

    <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="dashboard-content">
        <h1>System Administration Dashboard</h1>

        <!-- ================= DASHBOARD CARDS ================= -->
        <div class="dashboard-grid">

            <div class="stat-card">
                <div class="card-icon">👥</div>
                <h3>Total System Users</h3>
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <p>All registered users in AMMS</p>
                <a href="admin-users.php">Manage Users</a>
            </div>

            <div class="stat-card">
                <div class="card-icon">🌾</div>
                <h3>Total Listed Products</h3>
                <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                <p>All agricultural products listed</p>
                <a href="admin-products.php">Manage Products</a>
            </div>

            <div class="stat-card">
                <div class="card-icon">⏳</div>
                <h3>Pending Orders</h3>
                <div class="stat-number"><?php echo $stats['pending_orders']; ?></div>
                <p>Orders awaiting confirmation</p>
                <a href="admin-orders.php">View Pending</a>
            </div>

            <div class="stat-card">
                <div class="card-icon">🛒</div>
                <h3>Total Orders</h3>
                <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
                <p>All orders placed in the system</p>
                <a href="admin-orders.php">Manage Orders</a>
            </div>

            <div class="stat-card">
                <div class="card-icon">💰</div>
                <h3>Total Revenue</h3>
                <div class="stat-number">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <p>Revenue from completed orders</p>
                <a href="admin-reports.php">View Reports</a>
            </div>

        </div>

        
        <!-- ================= RECENT USERS ================= -->
        <div class="section">
            <h2>Recent User Registrations</h2>
            <?php if (!empty($all_users)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($all_users, 0, 5) as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo ucfirst($u['role']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $u['status']; ?>">
                                        <?php echo ucfirst($u['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- ================= RECENT ORDERS ================= -->
        <div class="section">
            <h2>Recent Orders</h2>
            <?php if (!empty($all_orders)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Buyer</th>
                            <th>Farmer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($all_orders, 0, 5) as $o): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($o['order_number']); ?></td>
                                <td><?php echo htmlspecialchars($o['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($o['farmer_name']); ?></td>
                                <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $o['order_status']; ?>">
                                        <?php echo ucfirst($o['order_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="container">
        <p><strong>Agri-Market Management System (AMMS)</strong></p>
        <p>Contact: +225 758 782 657 | Email: amosmayala14@gmail.com</p>
        <div class="footer-divider">
            &copy; 2026 AMMS — Empowering farmers, serving communities.
        </div>
    </div>
</footer>

</body>
</html>
