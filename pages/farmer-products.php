<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';

// Check if user is logged in and is a farmer
if (!$auth->isLoggedIn() || !$auth->hasRole('farmer')) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);
$farmer_products = $product->getFarmerProducts($user_id);
$categories = $product->getCategories();
$message = '';
$error = '';

// Handle add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $quantity = $_POST['quantity'] ?? 0;
    $unit = trim($_POST['unit'] ?? 'kg');
    $price = $_POST['price'] ?? 0;

    if (empty($name) || empty($category) || empty($quantity) || empty($price)) {
        $error = 'Please fill in all required fields';
    } else {
        $result = $product->addProduct($user_id, $name, $description, $category, $quantity, $unit, $price);
        if ($result['success']) {
            $message = 'Product added successfully!';
            $farmer_products = $product->getFarmerProducts($user_id);
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
    <title>My Products - AMMS</title>
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
                <li><span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <h1>My Products</h1>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="section">
                <h2>Add New Product</h2>
                <form method="POST" class="form-grid" onsubmit="return validateProductForm()">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity *</label>
                        <input type="number" id="quantity" name="quantity" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="unit">Unit *</label>
                        <select id="unit" name="unit" required>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="g">Gram (g)</option>
                            <option value="lb">Pound (lb)</option>
                            <option value="ton">Metric Ton</option>
                            <option value="l">Liter (L)</option>
                            <option value="ml">Milliliter (ml)</option>
                            <option value="pcs">Pieces</option>
                            <option value="box">Box</option>
                            <option value="bunch">Bunch</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price per Unit ($) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Describe your product (quality, origin, etc.)"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="grid-column: 1 / -1;">Add Product</button>
                </form>
            </div>

            <div class="section">
                <h2>Your Products (<?php echo count($farmer_products); ?>)</h2>
                <?php if (count($farmer_products) > 0): ?>
                    <div class="products-grid">
                        <?php foreach ($farmer_products as $p): ?>
                            <div class="product-card">
                                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($p['category']); ?></p>
                                <?php if ($p['description']): ?>
                                    <p class="description"><?php echo htmlspecialchars(substr($p['description'], 0, 100)); ?>...</p>
                                <?php endif; ?>
                                <div class="product-details">
                                    <p><strong>Price:</strong> $<?php echo number_format($p['price'], 2); ?>/<?php echo htmlspecialchars($p['unit']); ?></p>
                                    <p><strong>Quantity:</strong> <?php echo number_format($p['quantity'], 2); ?> <?php echo htmlspecialchars($p['unit']); ?></p>
                                    <p><strong>Status:</strong> <span class="badge badge-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></p>
                                </div>
                                <div class="product-actions">
                                    <a href="farmer-product-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="farmer-product-delete.php?id=<?php echo $p['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>You haven't added any products yet. <a href="farmer-products.php">Start by adding your first product above!</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>

    <script>
        function validateProductForm() {
            const name = document.getElementById('name').value.trim();
            const category = document.getElementById('category').value;
            const quantity = document.getElementById('quantity').value;
            const price = document.getElementById('price').value;

            if (!name || !category || !quantity || !price) {
                alert('Please fill in all required fields');
                return false;
            }

            if (isNaN(quantity) || quantity <= 0) {
                alert('Quantity must be a positive number');
                return false;
            }

            if (isNaN(price) || price <= 0) {
                alert('Price must be a positive number');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
