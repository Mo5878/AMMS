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
$filter_role = $_GET['role'] ?? '';
$users = [];

if ($filter_role && in_array($filter_role, ['farmer', 'buyer', 'admin'])) {
    $users = $admin->getUsersByRole($filter_role);
} else {
    $users = $admin->getAllUsers();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['status'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    $admin->updateUserStatus($user_id, $status);
    $users = $admin->getAllUsers();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - AMMS</title>
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
            <h1>User Management</h1>

            <div class="section">
                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <a href="admin-users.php" class="btn <?php echo !$filter_role ? 'btn-primary' : 'btn-secondary'; ?>">All Users</a>
                    <a href="admin-users.php?role=farmer" class="btn <?php echo $filter_role === 'farmer' ? 'btn-primary' : 'btn-secondary'; ?>">Farmers</a>
                    <a href="admin-users.php?role=buyer" class="btn <?php echo $filter_role === 'buyer' ? 'btn-primary' : 'btn-secondary'; ?>">Buyers</a>
                    <a href="admin-users.php?role=admin" class="btn <?php echo $filter_role === 'admin' ? 'btn-primary' : 'btn-secondary'; ?>">Admins</a>
                </div>

                <?php if (count($users) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Location</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td><?php echo htmlspecialchars($u['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($u['location']); ?></td>
                                    <td><?php echo ucfirst($u['role']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <select name="status" onchange="this.form.submit()">
                                                <option value="active" <?php echo $u['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="inactive" <?php echo $u['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $u['status']; ?>"><?php echo ucfirst($u['status']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>
</body>
</html>
