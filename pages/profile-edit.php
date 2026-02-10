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
$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $result = $auth->updateProfile($user_id, $name, $phone, $location);
    
    if ($result['success']) {
        $message = $result['message'];
        $user = $auth->getUserById($user_id);
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
    <title>Edit Profile - AMMS</title>
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
            <h1>Edit Profile</h1>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="profile-container">
                <div class="profile-section">
                    <form method="POST" class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address (Read-only)</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['location']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role (Read-only)</label>
                            <input type="text" id="role" name="role" value="<?php echo ucfirst($role); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="status">Status (Read-only)</label>
                            <input type="text" id="status" name="status" value="<?php echo ucfirst($user['status']); ?>" disabled>
                        </div>

                        <div style="grid-column: 1 / -1; display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="profile.php" class="btn btn-secondary">Cancel</a>
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
