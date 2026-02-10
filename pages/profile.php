<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);
$role = $auth->getUserRole();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.php" class="logo">AMMS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="<?php echo $role; ?>-dashboard.php">Dashboard</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <h1>My Profile</h1>

            <div class="profile-container">
                <div class="profile-section">
                    <h2>Profile Information</h2>
                    
                    <div class="profile-info">
                        <div class="info-item">
                            <label>Full Name:</label>
                            <p><?php echo htmlspecialchars($user['name']); ?></p>
                        </div>

                        <div class="info-item">
                            <label>Email Address:</label>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>

                        <div class="info-item">
                            <label>Phone Number:</label>
                            <p><?php echo htmlspecialchars($user['phone']); ?></p>
                        </div>

                        <div class="info-item">
                            <label>Location:</label>
                            <p><?php echo htmlspecialchars($user['location']); ?></p>
                        </div>

                        <div class="info-item">
                            <label>Role:</label>
                            <p><span class="badge badge-<?php echo $role; ?>"><?php echo ucfirst($role); ?></span></p>
                        </div>

                        <div class="info-item">
                            <label>Account Status:</label>
                            <p><span class="badge badge-<?php echo $user['status']; ?>"><?php echo ucfirst($user['status']); ?></span></p>
                        </div>

                        <div class="info-item">
                            <label>Member Since:</label>
                            <p><?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="profile-edit.php" class="btn btn-primary">Edit Profile</a>
                        <a href="change-password.php" class="btn btn-secondary">Change Password</a>
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
