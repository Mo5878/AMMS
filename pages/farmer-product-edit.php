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
$product_id = $_GET['id'] ?? 0;
$p = $product->getProductById($product_id);

// Verify ownership
if (!$p || $p['farmer_id'] !== $user_id) {
    header("Location: farmer-products.php");
    exit();
}

$categories = $product->getCategories();
$message = '';
$error = '';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $quantity = $_POST['quantity'] ?? 0;
    $unit = trim($_POST['unit'] ?? 'kg');
    $price = $_POST['price'] ?? 0;
    $status = $_POST['status'] ?? 'available';

    if (empty($name) || empty($category) || empty($quantity) || empty($price)) {
        $error = 'Please fill in all required fields';
    } else {
        $result = $product->updateProduct($product_id, $user_id, $name, $description, $category, $quantity, $unit, $price, $status);
        if ($result['success']) {
            $message = 'Product updated successfully!';
            $p = $product->getProductById($product_id);
        } else {
            $error = $result['message'];
        }
    }
}

$user = $auth->getUserById($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - AMMS</title>
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
            <h1>Edit Product</h1>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="section">
                <form method="POST" class="form-grid" onsubmit="return validateProductForm()">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $p['category'] === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity *</label>
                        <input type="number" id="quantity" name="quantity" step="0.01" min="0" value="<?php echo $p['quantity']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="unit">Unit *</label>
                        <select id="unit" name="unit" required>
                            <option value="kg" <?php echo $p['unit'] === 'kg' ? 'selected' : ''; ?>>Kilogram (kg)</option>
                            <option value="g" <?php echo $p['unit'] === 'g' ? 'selected' : ''; ?>>Gram (g)</option>
                            <option value="lb" <?php echo $p['unit'] === 'lb' ? 'selected' : ''; ?>>Pound (lb)</option>
                            <option value="ton" <?php echo $p['unit'] === 'ton' ? 'selected' : ''; ?>>Metric Ton</option>
                            <option value="l" <?php echo $p['unit'] === 'l' ? 'selected' : ''; ?>>Liter (L)</option>
                            <option value="ml" <?php echo $p['unit'] === 'ml' ? 'selected' : ''; ?>>Milliliter (ml)</option>
                            <option value="pcs" <?php echo $p['unit'] === 'pcs' ? 'selected' : ''; ?>>Pieces</option>
                            <option value="box" <?php echo $p['unit'] === 'box' ? 'selected' : ''; ?>>Box</option>
                            <option value="bunch" <?php echo $p['unit'] === 'bunch' ? 'selected' : ''; ?>>Bunch</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price per Unit ($) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $p['price']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="available" <?php echo $p['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="unavailable" <?php echo $p['status'] === 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                            <option value="out_of_stock" <?php echo $p['status'] === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Describe your product (quality, origin, etc.)"><?php echo htmlspecialchars($p['description'] ?? ''); ?></textarea>
                    </div>

                    <div style="grid-column: 1 / -1; display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">Update Product</button>
                        <a href="farmer-products.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
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

            if (isNaN(quantity) || quantity < 0) {
                alert('Quantity must be a non-negative number');
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
