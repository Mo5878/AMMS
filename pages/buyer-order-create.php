<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Product.php';
require_once '../includes/Order.php';

// Check if user is logged in and is a buyer
if (!$auth->isLoggedIn() || !$auth->hasRole('buyer')) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);
$product_id = $_GET['product_id'] ?? 0;
$p = $product->getProductById($product_id);

if (!$p) {
    header("Location: buyer-browse.php");
    exit();
}

$error = '';
$message = '';

// Handle order creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = $_POST['quantity'] ?? 0;

    if (!is_numeric($quantity) || $quantity <= 0) {
        $error = 'Please enter a valid quantity';
    } elseif ($quantity > $p['quantity']) {
        $error = 'Quantity exceeds available stock';
    } else {
        $items = [['product_id' => $product_id, 'quantity' => $quantity]];
        $result = $order->createOrder($user_id, $items);

        if ($result['success']) {
            header("Location: buyer-order-details.php?id=" . $result['order_id']);
            exit();
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
    <title>Place Order - AMMS</title>
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
            <h1>Place Order</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="order-form">
                <div class="section">
                    <h2>Product Details</h2>
                    <div class="info-box">
                        <p><strong>Product:</strong> <?php echo htmlspecialchars($p['name']); ?></p>
                        <p><strong>Farmer:</strong> <?php echo htmlspecialchars($p['farmer_name']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($p['category']); ?></p>
                        <p><strong>Price per Unit:</strong> $<?php echo number_format($p['price'], 2); ?>/<?php echo htmlspecialchars($p['unit']); ?></p>
                        <p><strong>Available Quantity:</strong> <?php echo number_format($p['quantity'], 2); ?> <?php echo htmlspecialchars($p['unit']); ?></p>
                        <?php if ($p['description']): ?>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($p['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="section">
                    <h2>Your Details</h2>
                    <div class="info-box">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?></p>
                    </div>
                </div>

                <div class="section">
                    <h2>Order Details</h2>
                    <form method="POST" onsubmit="return validateOrderForm()">
                        <div class="form-group">
                            <label for="quantity">Quantity to Order *</label>
                            <input type="number" id="quantity" name="quantity" step="0.01" min="0.01" max="<?php echo $p['quantity']; ?>" required>
                            <small>Max: <?php echo number_format($p['quantity'], 2); ?> <?php echo htmlspecialchars($p['unit']); ?></small>
                        </div>

                        <div class="form-group">
                            <p><strong>Total Amount:</strong> $<span id="totalAmount">0.00</span></p>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Confirm Order</button>
                            <a href="buyer-browse.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Agri-Market Management System. All rights reserved.</p>
    </footer>

    <script>
        const pricePerUnit = <?php echo $p['price']; ?>;

        document.getElementById('quantity').addEventListener('input', function() {
            const quantity = parseFloat(this.value) || 0;
            const total = (quantity * pricePerUnit).toFixed(2);
            document.getElementById('totalAmount').textContent = total;
        });

        function validateOrderForm() {
            const quantity = document.getElementById('quantity').value;
            const maxQuantity = <?php echo $p['quantity']; ?>;

            if (!quantity || isNaN(quantity) || quantity <= 0) {
                alert('Please enter a valid quantity');
                return false;
            }

            if (parseFloat(quantity) > maxQuantity) {
                alert('Quantity exceeds available stock');
                return false;
            }

            return confirm('Confirm this order for ' + quantity + ' <?php echo htmlspecialchars($p['unit']); ?>?');
        }

        // Initialize total on page load
        document.getElementById('quantity').dispatchEvent(new Event('input'));
    </script>
</body>
</html>
