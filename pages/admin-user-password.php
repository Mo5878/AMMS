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

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $result = $auth->adminResetPassword($user_id, $new_password, $confirm_password);
    
    if ($result['success']) {
        $message = $result['message'];
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
    <title>Reset User Password - AMMS</title>
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
            <h1>Reset User Password</h1>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="profile-container">
                <div class="profile-section" style="max-width: 500px;">
                    <h2>Reset Password for: <?php echo htmlspecialchars($target_user['name']); ?></h2>
                    <p style="color: #7f8c8d; margin-bottom: 20px;">Email: <?php echo htmlspecialchars($target_user['email']); ?></p>

                    <form method="POST" onsubmit="return validatePasswordForm()">
                        <div class="form-group">
                            <label for="new_password">New Password *</label>
                            <input type="password" id="new_password" name="new_password" required>
                            <small>Minimum 6 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                            <a href="admin-user-edit.php?id=<?php echo $target_user['id']; ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>

                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color); background-color: var(--light-gray); padding: 15px; border-radius: 4px;">
                        <strong style="color: var(--warning-color);">⚠️ Important:</strong>
                        <p style="margin-top: 10px; font-size: 0.95em;">
                            This will reset the password for user <strong><?php echo htmlspecialchars($target_user['name']); ?></strong>. 
                            They will need to use the new password to log in.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>

    <script>
        function validatePasswordForm() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword.length < 6) {
                alert('Password must be at least 6 characters long');
                return false;
            }

            if (newPassword !== confirmPassword) {
                alert('Passwords do not match');
                return false;
            }

            return confirm('Are you sure you want to reset this user\'s password?');
        }
    </script>
</body>
</html>
