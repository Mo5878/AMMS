<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Admin.php';

// Check if user is logged in and is admin
if (!$auth->isLoggedIn() || !$auth->hasRole('admin')) {
    header("Location: login.php");
    exit();
}

$admin_user = $auth->getUserById($auth->getUserId());
$user_id = $_GET['id'] ?? 0;
$target_user = $auth->getUserById($user_id);

// Verify user exists
if (!$target_user) {
    header("Location: admin-users.php");
    exit();
}

$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $result = $auth->adminEditUserProfile($user_id, $name, $email, $phone, $location);
    
    if ($result['success']) {
        $message = $result['message'];
        $target_user = $auth->getUserById($user_id);
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - AMMS</title>
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
                <li><a href="profile.php">My Profile</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($admin_user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <h1>Edit User Profile</h1>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="profile-container">
                <div class="profile-section">
                    <h2>User Information</h2>
                    
                    <form method="POST" class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($target_user['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($target_user['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($target_user['phone']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($target_user['location']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role (Read-only)</label>
                            <input type="text" id="role" name="role" value="<?php echo ucfirst($target_user['role']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="status">Status (Read-only)</label>
                            <input type="text" id="status" name="status" value="<?php echo ucfirst($target_user['status']); ?>" disabled>
                        </div>

                        <div style="grid-column: 1 / -1; display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="admin-user-password.php?id=<?php echo $target_user['id']; ?>" class="btn btn-warning">Reset Password</a>
                            <a href="admin-users.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>

                <div class="profile-section">
                    <h2>Additional Information</h2>
                    <div class="info-item">
                        <label>Account Created:</label>
                        <p><?php echo date('F d, Y at h:i A', strtotime($target_user['created_at'])); ?></p>
                    </div>
                    <div class="info-item">
                        <label>Last Updated:</label>
                        <p><?php echo date('F d, Y at h:i A', strtotime($target_user['updated_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>
</body>
</html>
