<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

// If already logged in, redirect to dashboard
if ($auth->isLoggedIn()) {
    $role = $auth->getUserRole();
    header("Location: ..pages/{$role}-dashboard.php");
    exit();
}

$error = '';
$default_role = $_GET['role'] ?? 'buyer';

if (!in_array($default_role, ['farmer', 'buyer'])) {
    $default_role = 'buyer';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $role = $_POST['role'] ?? 'buyer';

    // Client-side validation already done, do server-side
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($location)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $result = $auth->register($name, $email, $password, $phone, $location, $role);

        if ($result['success']) {
            // Auto-login after registration
            $login_result = $auth->login($email, $password);
            if ($login_result['success']) {
                header("Location: {$role}-dashboard.php");
                exit();
            }
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-form">
            <h1>Register to AMMS</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" id="registerForm" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="location">Location/Address:</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <div class="form-group">
                    <label for="role">Register as:</label>
                    <select id="role" name="role" required>
                        <option value="buyer" <?php echo $default_role === 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                        <option value="farmer" <?php echo $default_role === 'farmer' ? 'selected' : ''; ?>>Farmer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <small>Minimum 6 characters</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Register</button>
            </form>

            <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
            <p class="auth-link"><a href="../index.php">Back to Home</a></p>
        </div>
    </div>

    <script>
        function validateForm() {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const location = document.getElementById('location').value.trim();
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;

            if (!name || !email || !phone || !location || !password || !confirm_password) {
                alert('Please fill in all fields');
                return false;
            }

            if (!email.includes('@')) {
                alert('Please enter a valid email');
                return false;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters');
                return false;
            }

            if (password !== confirm_password) {
                alert('Passwords do not match');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
