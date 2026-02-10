<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';

// Check if user is logged in and is a buyer
if (!$auth->isLoggedIn() || !$auth->hasRole('buyer')) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);

// Get search/filter parameters
$search_name = $_GET['search'] ?? '';
$filter_category = $_GET['category'] ?? '';
$filter_price_min = $_GET['price_min'] ?? 0;
$filter_price_max = $_GET['price_max'] ?? 999999;

$categories = $product->getCategories();
$products = [];

// Perform search if parameters are provided
if ($search_name || $filter_category || ($filter_price_min && $filter_price_min > 0) || ($filter_price_max && $filter_price_max < 999999)) {
    $products = $product->searchProducts($search_name, $filter_category, $filter_price_min, $filter_price_max, 'available');
} else {
    $products = $product->getAllProducts(100);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products - AMMS</title>
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
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <h1>Browse Products</h1>

            <div class="search-section">
                <form method="GET" class="search-form" id="searchForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="search">Search by Name</label>
                            <input type="text" id="search" name="search" placeholder="e.g. Tomatoes, Rice..." value="<?php echo htmlspecialchars($search_name); ?>">
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $filter_category === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price_min">Min Price ($)</label>
                            <input type="number" id="price_min" name="price_min" step="0.01" min="0" value="<?php echo htmlspecialchars($filter_price_min); ?>">
                        </div>

                        <div class="form-group">
                            <label for="price_max">Max Price ($)</label>
                            <input type="number" id="price_max" name="price_max" step="0.01" min="0" value="<?php echo htmlspecialchars($filter_price_max); ?>">
                        </div>

                        <div style="display: flex; gap: 10px; align-items: flex-end;">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="buyer-browse.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="section">
                <h2>Available Products (<?php echo count($products); ?>)</h2>
                
                <?php if (count($products) > 0): ?>
                    <div class="products-grid">
                        <?php foreach ($products as $p): ?>
                            <div class="product-card">
                                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($p['category']); ?></p>
                                <p class="farmer">by <?php echo htmlspecialchars($p['farmer_name']); ?></p>
                                <?php if ($p['description']): ?>
                                    <p class="description"><?php echo htmlspecialchars(substr($p['description'], 0, 100)); ?>...</p>
                                <?php endif; ?>
                                <div class="product-details">
                                    <p><strong>Price:</strong> $<?php echo number_format($p['price'], 2); ?>/<?php echo htmlspecialchars($p['unit']); ?></p>
                                    <p><strong>Available:</strong> <?php echo number_format($p['quantity'], 2); ?> <?php echo htmlspecialchars($p['unit']); ?></p>
                                </div>
                                <div class="product-actions">
                                    <a href="buyer-order-create.php?product_id=<?php echo $p['id']; ?>" class="btn btn-primary">Order Now</a>
                                    <a href="buyer-product-view.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary">Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No products found matching your criteria. Try adjusting your search filters.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>
</body>
</html>
