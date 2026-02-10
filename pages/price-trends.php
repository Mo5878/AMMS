<?php
require_once '../config/config.php';
require_once '../includes/DashboardHelpers.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';

$auth = new Auth($conn);
$product = new Product($conn);

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);
$user_role = $user['role'];

// Get filter parameters
$filter_category = isset($_GET['category']) ? $_GET['category'] : '';
$days = isset($_GET['days']) ? intval($_GET['days']) : 30;

// Get all products for category list
$all_products = $product->getAllProducts(1000);
$categories = !empty($all_products) ? array_unique(array_column($all_products, 'category')) : [];
sort($categories);

// Initialize arrays
$price_trends = [];
$category_trends = [];

// Get price trends data - with error handling
try {
    if ($filter_category && in_array($filter_category, $categories)) {
        $price_trends = $product->getCategoryPriceHistory($filter_category, 100);
        $page_title = "Price Trends - " . htmlspecialchars($filter_category);
    } else {
        $price_trends = $product->getAllPriceTrends($days);
        $page_title = "Price Trends - Last " . $days . " Days";
    }

    // Get category trends summary
    $category_trends = $product->getCategoryTrendsSummary($days);
} catch (Exception $e) {
    // If price_history table doesn't exist, show empty state
    $price_trends = [];
    $category_trends = [];
}

// Calculate statistics
$total_price_changes = count($price_trends);
$price_changes_by_category = [];
foreach ($price_trends as $trend) {
    $cat = $trend['category'];
    if (!isset($price_changes_by_category[$cat])) {
        $price_changes_by_category[$cat] = 0;
    }
    $price_changes_by_category[$cat]++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - AMMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.php" class="logo">AMMS</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="<?php 
                    if ($user_role === 'admin') echo 'admin-dashboard.php';
                    elseif ($user_role === 'farmer') echo 'farmer-dashboard.php';
                    else echo 'buyer-dashboard.php';
                ?>">Dashboard</a></li>
                <li><a href="price-trends.php">Price Trends</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <?php if ($user_role === 'admin'): ?>
                    <li class="sidebar-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="sidebar-item"><a href="price-trends.php" class="active">Price Trends</a></li>
                    <li class="sidebar-item"><a href="admin-users.php">Users</a></li>
                    <li class="sidebar-item"><a href="admin-products.php">Products</a></li>
                    <li class="sidebar-item"><a href="admin-orders.php">Orders</a></li>
                    <li class="sidebar-item"><a href="admin-reports.php">Reports</a></li>
                    <li class="sidebar-item"><a href="profile.php">Profile</a></li>
                <?php elseif ($user_role === 'farmer'): ?>
                    <li class="sidebar-item"><a href="farmer-dashboard.php">Dashboard</a></li>
                    <li class="sidebar-item"><a href="farmer-products.php">My Products</a></li>
                    <li class="sidebar-item"><a href="price-trends.php" class="active">Price Trends</a></li>
                    <li class="sidebar-item"><a href="farmer-orders.php">Orders</a></li>
                    <li class="sidebar-item"><a href="farmer-market-prices.php"></a></li>
                    <li class="sidebar-item"><a href="farmer-reports.php">Reports</a></li>
                    <li class="sidebar-item"><a href="profile.php">Profile</a></li>
                <?php elseif ($user_role === 'buyer'): ?>
                    <li class="sidebar-item"><a href="buyer-dashboard.php">Dashboard</a></li>
                    <li class="sidebar-item"><a href="buyer-browse.php">Browse Products</a></li>
                    <li class="sidebar-item"><a href="price-trends.php" class="active">Price Trends</a></li>
                    <li class="sidebar-item"><a href="buyer-orders.php">My Orders</a></li>
                    <li class="sidebar-item"><a href="profile.php">Profile</a></li>
                <?php endif; ?>
            </nav>
        </aside>
        <div class="dashboard-content" style="width: 100%;">
            <div class="content">
            <h1><?php echo $page_title; ?></h1>
            <p class="subtitle">Track and analyze price changes across all products</p>

            <?php if (empty($price_trends) && empty($category_trends)): ?>
                <div class="section" style="background-color: #e8f4f8; border-left: 4px solid #3498db; padding: 20px; border-radius: 4px; margin-bottom: 20px;">
                    <p style="color: #2c3e50; margin: 0;">
                        <strong>No price changes yet.</strong> Price trends will appear here once farmers update product prices. 
                        <a href="<?php echo $user_role === 'farmer' ? 'farmer-products.php' : '#'; ?>" style="color: #3498db; text-decoration: underline;">
                            <?php echo $user_role === 'farmer' ? 'Update your product prices' : ''; ?>
                        </a>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Time Range Filter -->
            <div class="section">
                <h2>Filter Options</h2>
                <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                    <label style="font-weight: 600;">Time Range:</label>
                    <a href="price-trends.php?days=7" class="btn <?php echo $days === 7 ? 'btn-primary' : 'btn-secondary'; ?>">Last 7 Days</a>
                    <a href="price-trends.php?days=30" class="btn <?php echo $days === 30 ? 'btn-primary' : 'btn-secondary'; ?>">Last 30 Days</a>
                    <a href="price-trends.php?days=90" class="btn <?php echo $days === 90 ? 'btn-primary' : 'btn-secondary'; ?>">Last 90 Days</a>
                    <a href="price-trends.php?days=365" class="btn <?php echo $days === 365 ? 'btn-primary' : 'btn-secondary'; ?>">Last Year</a>
                </div>

                <label style="font-weight: 600; display: block; margin-bottom: 10px;">By Category:</label>
                <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                    <a href="price-trends.php?days=<?php echo $days; ?>" class="btn <?php echo !$filter_category ? 'btn-primary' : 'btn-secondary'; ?>">All Categories</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="price-trends.php?category=<?php echo urlencode($cat); ?>&days=<?php echo $days; ?>" class="btn <?php echo $filter_category === $cat ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo htmlspecialchars($cat); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="section">
                <h2>Summary Statistics</h2>
                <div class="stat-row">
                    <div class="stat-box">
                        <span class="stat-label">Total Price Changes</span>
                        <span class="stat-value" style="color: #e74c3c;"><?php echo $total_price_changes; ?></span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-label">Active Categories</span>
                        <span class="stat-value" style="color: #3498db;"><?php echo count($price_changes_by_category); ?></span>
                    </div>
                    <?php 
                    if (!empty($category_trends)) {
                        $total_min = min(array_column($category_trends, 'lowest_price_recorded'));
                        $total_max = max(array_column($category_trends, 'highest_price_recorded'));
                    ?>
                        <div class="stat-box">
                            <span class="stat-label">Lowest Price Recorded</span>
                            <span class="stat-value" style="color: #27ae60;">$<?php echo number_format($total_min, 2); ?></span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-label">Highest Price Recorded</span>
                            <span class="stat-value" style="color: #9b59b6;">$<?php echo number_format($total_max, 2); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Category Trends Chart -->
            <?php if (!empty($category_trends)): ?>
            <div class="section">
                <h2>Price Changes by Category</h2>
                <div style="position: relative; height: 300px; margin: 20px 0;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            <?php endif; ?>

            <!-- Category Trends Table -->
            <?php if (!empty($category_trends)): ?>
            <div class="section">
                <h2>Category Trends Summary</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Price Changes</th>
                            <th>Lowest Price</th>
                            <th>Highest Price</th>
                            <th>Current Avg Price</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($category_trends as $ct): 
                            $price_diff = $ct['highest_price_recorded'] - $ct['lowest_price_recorded'];
                            $price_percent = ($price_diff / $ct['lowest_price_recorded']) * 100;
                            $trend_color = $price_percent > 10 ? '#e74c3c' : ($price_percent > 5 ? '#f39c12' : '#27ae60');
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($ct['category']); ?></strong></td>
                                <td><?php echo $ct['price_changes']; ?></td>
                                <td>$<?php echo number_format($ct['lowest_price_recorded'], 2); ?></td>
                                <td>$<?php echo number_format($ct['highest_price_recorded'], 2); ?></td>
                                <td>$<?php echo number_format($ct['avg_current_price'], 2); ?></td>
                                <td>
                                    <span style="color: <?php echo $trend_color; ?>; font-weight: 600;">
                                        <?php echo number_format($price_percent, 1); ?>% variance
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Recent Price Changes -->
            <div class="section">
                <h2>Recent Price Changes <?php if ($filter_category) echo "(" . htmlspecialchars($filter_category) . ")"; ?></h2>
                <?php if (count($price_trends) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Farmer</th>
                                    <th>Old Price</th>
                                    <th>New Price</th>
                                    <th>Change</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($price_trends as $trend): 
                                    $change_details = formatPriceChange($trend['old_price'], $trend['new_price']);
                                ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($trend['product_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($trend['category']); ?></td>
                                        <td><?php echo htmlspecialchars($trend['farmer_name']); ?></td>
                                        <td>$<?php echo number_format($trend['old_price'], 2); ?></td>
                                        <td>$<?php echo number_format($trend['new_price'], 2); ?></td>
                                        <td>
                                            <span style="color: <?php echo $change_details['color']; ?>; font-weight: 600;">
                                                <?php echo $change_details['formatted']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($trend['changed_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; padding: 20px; background-color: #f8f9fa; border-radius: 4px;">
                        No price changes recorded in this period.
                    </p>
                <?php endif; ?>
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
        // Chart.js configuration for category trends
        <?php if (!empty($category_trends)): ?>
        const categoryLabels = [<?php echo "'" . implode("','", array_map(fn($ct) => htmlspecialchars($ct['category']), $category_trends)) . "'"; ?>];
        const categoryData = [<?php echo implode(",", array_column($category_trends, 'price_changes')); ?>];
        
        const ctx = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Price Changes Count',
                    data: categoryData,
                    backgroundColor: [
                        '#3498db',
                        '#2ecc71',
                        '#e74c3c',
                        '#f39c12',
                        '#9b59b6',
                        '#1abc9c',
                        '#34495e'
                    ],
                    borderRadius: 5,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
