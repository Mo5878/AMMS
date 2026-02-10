<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/MarketPrice.php';

// Check if user is logged in and is a farmer
if (!$auth->isLoggedIn() || !$auth->hasRole('farmer')) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);

$market_price = new MarketPrice($conn);
// Farmers may view prices but cannot submit  values.
// Submissions are restricted to buyers and admins (handled by MarketPrice class).
$message = '';
$error = '';

// Get user's submissions
$user_submissions = $market_price->getUserPriceSubmissions($user_id, 50);

// Get farmer's product categories
$stmt = $conn->prepare("SELECT DISTINCT category FROM products WHERE farmer_id = ? ORDER BY category");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$categories_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$categories = array_column($categories_result, 'category');

// Get current  for reference
$all_categories = $market_price->getAllCategories();
if (empty($all_categories)) {
    $all_categories = $categories;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Management - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.php" class="logo">AMMS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="farmer-dashboard.php">Dashboard</a></li>
                <li><a href="farmer-products.php">My Products</a></li>
                <li><a href="farmer-orders.php">Orders</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <li class="sidebar-item"><a href="farmer-dashboard.php">Dashboard</a></li>
                <li class="sidebar-item"><a href="farmer-products.php">My Products</a></li>
                <li class="sidebar-item"><a href="price-trends.php">Price Trends</a></li>
                <li class="sidebar-item"><a href="farmer-orders.php">Orders</a></li>
                <li class="sidebar-item"><a href="farmer-market-prices.php">Market Analysis</a></li>
                <li class="sidebar-item"><a href="farmer-set-market-price.php" class="active">Market Pricing</a></li>
                <li class="sidebar-item"><a href="farmer-reports.php">Reports</a></li>
                <li class="sidebar-item"><a href="profile.php">Profile</a></li>
            </nav>
        </aside>

        <div class="dashboard-content">
            <div class="content">
                <h1> Management</h1>
                <p class="subtitle">Submit and manage fair  for your product categories</p>

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
                    <h2>Submit </h2>
                    <p>Only buyers and administrators can submit or update official . As a farmer you can view approved  and trends below.</p>
                </div>

                <div class="section">
                    <h2>Your Price Submissions</h2>
                    
                    <?php if (empty($user_submissions)): ?>
                        <p>You haven't submitted any  yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Suggested Price</th>
                                        <th>Status</th>
                                        <th>Approved Price</th>
                                        <th>Submitted Date</th>
                                        <th>Admin Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($user_submissions as $submission): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($submission['product_category']); ?></strong></td>
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
                                                <small>
                                                    <?php echo $submission['admin_comments'] ? htmlspecialchars($submission['admin_comments']) : '(none)'; ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="section">
                    <h2> Reference</h2>
                    <p>Current approved  by category:</p>
                    
                    <div class="price-grid">
                        <?php 
                        $has_prices = false;
                        foreach ($all_categories as $cat):
                            $current_price = $market_price->getCurrentMarketPrice($cat);
                            if ($current_price):
                                $has_prices = true;
                        ?>
                            <div class="price-card">
                                <h3><?php echo htmlspecialchars($cat); ?></h3>
                                <div class="price-display">
                                    <span class="price">$<?php echo number_format($current_price['approved_price'], 2); ?></span>
                                </div>
                                <small style="display: block; margin-top: 10px; color: #666;">
                                    Approved: <?php echo date('M d, Y', strtotime($current_price['approved_at'])); ?>
                                </small>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>

                    <?php if (!$has_prices): ?>
                        <p style="margin-top: 20px; color: #666;">No approved  yet. Be the first to suggest fair prices!</p>
                    <?php endif; ?>
                </div>

                <div class="section">
                    <h2>How It Works</h2>
                    <ul style="line-height: 1.8;">
                        <li><strong>Submit Prices:</strong> Suggest fair  for your product categories</li>
                        <li><strong>Admin Review:</strong> System administrators verify and approve your submissions</li>
                        <li><strong>Market Setting:</strong> Approved prices become official market reference prices</li>
                        <li><strong>Price Updates:</strong> Submit new prices as market conditions change</li>
                        <li><strong>Track Status:</strong> Monitor all your submissions and their approval status</li>
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
</body>
</html>
