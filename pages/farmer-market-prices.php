<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';

// Check if user is logged in and is a farmer or admin
if (!$auth->isLoggedIn() || (!$auth->hasRole('farmer') && !$auth->hasRole('admin'))) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);

// Get all products to analyze  by category
$all_products = $product->getAllProducts(1000);

// Group products by category and calculate average prices
$market_prices = [];
foreach ($all_products as $p) {
    $category = $p['category'];
    if (!isset($market_prices[$category])) {
        $market_prices[$category] = [
            'prices' => [],
            'products' => []
        ];
    }
    $market_prices[$category]['prices'][] = $p['price'];
    $market_prices[$category]['products'][] = $p;
}

// Calculate statistics for each category
$category_stats = [];
foreach ($market_prices as $category => $data) {
    $prices = $data['prices'];
    $category_stats[$category] = [
        'avg_price' => array_sum($prices) / count($prices),
        'min_price' => min($prices),
        'max_price' => max($prices),
        'product_count' => count($prices),
        'products' => $data['products']
    ];
}

// Sort by category name
ksort($category_stats);

// Get filter
$filter_category = isset($_GET['category']) ? $_GET['category'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> - AMMS</title>
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
                <li class="sidebar-item"><a href="farmer-market-prices.php" class="active"></a></li>
                <li class="sidebar-item"><a href="farmer-reports.php">Reports</a></li>
                <li class="sidebar-item"><a href="profile.php">Profile</a></li>
            </nav>
        </aside>
        <div class="dashboard-content">
            <div class="content">
            <h1> Analysis</h1>
            <p class="subtitle">View current  and trends across all product categories</p>

            <div class="section">
                <h2>Category Overview</h2>
                
                <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                    <a href="farmer-market-prices.php" class="btn <?php echo !$filter_category ? 'btn-primary' : 'btn-secondary'; ?>">All Categories</a>
                    <?php foreach (array_keys($category_stats) as $cat): ?>
                        <a href="farmer-market-prices.php?category=<?php echo urlencode($cat); ?>" class="btn <?php echo $filter_category === $cat ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo htmlspecialchars($cat); ?></a>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($category_stats)): ?>
                    <p>No products available in the market yet.</p>
                <?php else: ?>
                    <div class="price-grid">
                        <?php 
                        $display_categories = $filter_category ? [$filter_category => $category_stats[$filter_category]] : $category_stats;
                        foreach ($display_categories as $category => $stats): 
                        ?>
                            <div class="price-card">
                                <h3><?php echo htmlspecialchars($category); ?></h3>
                                <div class="price-info">
                                    <div class="price-item">
                                        <span class="label">Average Price:</span>
                                        <span class="price">$<?php echo number_format($stats['avg_price'], 2); ?></span>
                                    </div>
                                    <div class="price-item">
                                        <span class="label">Minimum:</span>
                                        <span class="price" style="color: #27ae60;">$<?php echo number_format($stats['min_price'], 2); ?></span>
                                    </div>
                                    <div class="price-item">
                                        <span class="label">Maximum:</span>
                                        <span class="price" style="color: #e74c3c;">$<?php echo number_format($stats['max_price'], 2); ?></span>
                                    </div>
                                    <div class="price-item">
                                        <span class="label">Products Listed:</span>
                                        <span class="count"><?php echo $stats['product_count']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($filter_category && isset($category_stats[$filter_category])): ?>
                        <div class="section" style="margin-top: 40px;">
                            <h2>Products in <?php echo htmlspecialchars($filter_category); ?> Category</h2>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Farmer</th>
                                        <th>Price</th>
                                        <th>Unit</th>
                                        <th>Available Qty</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($category_stats[$filter_category]['products'] as $p): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($p['farmer_name']); ?></td>
                                            <td>$<?php echo number_format($p['price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($p['unit']); ?></td>
                                            <td><?php echo number_format($p['quantity'], 2); ?></td>
                                            <td><span class="badge badge-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>Price Comparison Tips</h2>
                <ul style="line-height: 1.8;">
                    <li><strong>Monitor Trends:</strong> Check  regularly to ensure your products are competitively priced</li>
                    <li><strong>Price Your Products:</strong> Use the average price as a baseline, but adjust based on quality and demand</li>
                    <li><strong>Adjust Strategy:</strong> Consider pricing lower during peak harvest seasons and higher during off-seasons</li>
                    <li><strong>Quality Matters:</strong> Premium quality products may command higher prices</li>
                </ul>
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
