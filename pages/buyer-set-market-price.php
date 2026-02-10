<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/MarketPrice.php';

// Check if user is logged in and is a buyer
if (!$auth->isLoggedIn() || !$auth->hasRole('buyer')) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);

$market_price = new MarketPrice($conn);
$message = '';
$error = '';

// Handle price submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_price'])) {
    $category = trim($_POST['category']);
    $suggested_price = floatval($_POST['suggested_price']);

    $result = $market_price->submitMarketPrice($user_id, 'buyer', $category, $suggested_price);
    
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// Get user's submissions
$user_submissions = $market_price->getUserPriceSubmissions($user_id, 50);

// Get available categories from products
$stmt = $conn->prepare("SELECT DISTINCT category FROM products WHERE status = 'available' ORDER BY category");
$stmt->execute();
$categories_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$categories = array_column($categories_result, 'category');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit  - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.php" class="logo">AMMS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="buyer-dashboard.php">Dashboard</a></li>
                <li><a href="buyer-browse.php">Browse Products</a></li>
                <li><a href="buyer-orders.php">My Orders</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <li class="sidebar-item"><a href="buyer-dashboard.php">Dashboard</a></li>
                <li class="sidebar-item"><a href="buyer-browse.php">Browse Products</a></li>
                <li class="sidebar-item"><a href="buyer-orders.php">My Orders</a></li>
                <li class="sidebar-item"><a href="buyer-set-market-price.php" class="active"></a></li>
                <li class="sidebar-item"><a href="profile.php">Profile</a></li>
            </nav>
        </aside>

        <div class="dashboard-content">
            <div class="content">
                <h1>Submit </h1>
                <p class="subtitle">Help us set fair  by suggesting prices for product categories</p>

                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <strong>Success!</strong> <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="section">
                    <h2>Submit a </h2>
                    <form method="POST" class="form">
                        <div class="form-group">
                            <label for="category">Product Category *</label>
                            <select id="category" name="category" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>">
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="suggested_price">Suggested Price (per unit) *</label>
                            <input type="number" id="suggested_price" name="suggested_price" step="0.01" min="0.01" required placeholder="Enter price">
                            <small>Enter the price you think is fair for this product category</small>
                        </div>

                        <button type="submit" name="submit_price" class="btn btn-primary">
                            Submit 
                        </button>
                    </form>
                </div>

                <div class="section">
                    <h2>Your Price Submissions</h2>
                    
                    <?php if (empty($user_submissions)): ?>
                        <p>You haven't submitted any  yet.</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Suggested Price</th>
                                    <th>Status</th>
                                    <th>Approved Price</th>
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_submissions as $submission): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($submission['product_category']); ?></td>
                                        <td>$<?php echo number_format($submission['suggested_price'], 2); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($submission['status']); ?>">
                                                <?php echo ucfirst($submission['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $submission['approved_price'] ? '$' . number_format($submission['approved_price'], 2) : '-'; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="showDetails(<?php echo $submission['id']; ?>)" class="btn btn-small btn-secondary">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <div class="section">
                    <h2>How It Works</h2>
                    <ul style="line-height: 1.8;">
                        <li><strong>Submit Suggestions:</strong> Propose fair  for product categories based on your experience</li>
                        <li><strong>Admin Review:</strong> System administrators review your submissions</li>
                        <li><strong>Approval:</strong> Once approved, your suggested price becomes part of the official </li>
                        <li><strong>Market Impact:</strong> Your submissions help set fair prices across the market</li>
                        <li><strong>Track History:</strong> Monitor all your submissions and their approval status</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p><strong>Agri-Market Management System (AMMS)</strong></p>
            <p>Contact: +225 758 782 657 | Email: amosmayala14@gmail.com</p>
            <div class="footer-divider">
                &copy; 2026 Agri-Market Management System. All rights reserved. | Empowering farmers, serving communities, growing together.
            </div>
        </div>
    </footer>

    <script>
        function showDetails(submissionId) {
            alert('Submission ID: ' + submissionId + '\nDetails feature coming soon!');
        }
    </script>
</body>
</html>
