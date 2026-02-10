<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/Order.php';

// Check if user is logged in and is a farmer or admin
if (!$auth->isLoggedIn() || (!$auth->hasRole('farmer') && !$auth->hasRole('admin'))) {
    header("Location: login.php");
    exit();
}

$user_id = $auth->getUserId();
$user = $auth->getUserById($user_id);
$farmer_orders = $order->getFarmerOrders($user_id);

// Filter only paid orders for report
$paid_orders = array_filter($farmer_orders, function($o) {
    return $o['payment_status'] === 'paid';
});

// Calculate statistics
$total_revenue = array_sum(array_column($paid_orders, 'total_amount'));
$total_orders = count($paid_orders);
$average_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;

// Handle download request
if (isset($_GET['download'])) {
    $format = $_GET['download'];
    
    if ($format === 'csv') {
        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="farmer_report_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, ['Farmer Sales Report']);
        fputcsv($output, ['Generated on: ' . date('F d, Y H:i:s')]);
        fputcsv($output, ['Farmer: ' . $user['name']]);
        fputcsv($output, ['Email: ' . $user['email']]);
        fputcsv($output, ['']);
        
        // Summary
        fputcsv($output, ['Summary']);
        fputcsv($output, ['Total Revenue', '$' . number_format($total_revenue, 2)]);
        fputcsv($output, ['Total Orders', $total_orders]);
        fputcsv($output, ['Average Order Value', '$' . number_format($average_order_value, 2)]);
        fputcsv($output, ['']);
        
        // Orders
        fputcsv($output, ['Order Details']);
        fputcsv($output, ['Order #', 'Buyer', 'Amount', 'Status', 'Payment Status', 'Date']);
        
        foreach ($paid_orders as $o) {
            fputcsv($output, [
                $o['order_number'],
                $o['buyer_name'],
                '$' . number_format($o['total_amount'], 2),
                ucfirst($o['order_status']),
                ucfirst($o['payment_status']),
                date('M d, Y', strtotime($o['created_at']))
            ]);
        }
        
        fclose($output);
        exit();
    } 
    elseif ($format === 'pdf') {
        // Generate simple text-based PDF-like report (can be upgraded with TCPDF library)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="farmer_report_' . date('Y-m-d_H-i-s') . '.pdf"');
        
        $content = "FARMER SALES REPORT\n";
        $content .= "Generated: " . date('F d, Y H:i:s') . "\n\n";
        $content .= "Farmer: " . $user['name'] . "\n";
        $content .= "Email: " . $user['email'] . "\n";
        $content .= "Location: " . $user['location'] . "\n";
        $content .= str_repeat("=", 60) . "\n\n";
        
        $content .= "SUMMARY STATISTICS\n";
        $content .= str_repeat("-", 60) . "\n";
        $content .= "Total Revenue (Paid Orders): $" . number_format($total_revenue, 2) . "\n";
        $content .= "Total Orders Completed: " . $total_orders . "\n";
        $content .= "Average Order Value: $" . number_format($average_order_value, 2) . "\n";
        $content .= str_repeat("=", 60) . "\n\n";
        
        $content .= "ORDER DETAILS\n";
        $content .= str_repeat("-", 60) . "\n";
        
        foreach ($paid_orders as $o) {
            $content .= "Order #: " . $o['order_number'] . "\n";
            $content .= "Buyer: " . $o['buyer_name'] . "\n";
            $content .= "Amount: $" . number_format($o['total_amount'], 2) . "\n";
            $content .= "Status: " . ucfirst($o['order_status']) . "\n";
            $content .= "Payment: " . ucfirst($o['payment_status']) . "\n";
            $content .= "Date: " . date('M d, Y', strtotime($o['created_at'])) . "\n";
            $content .= str_repeat("-", 60) . "\n";
        }
        
        echo $content;
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - AMMS</title>
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
                <li class="sidebar-item"><a href="farmer-market-prices.php"></a></li>
                <li class="sidebar-item"><a href="farmer-reports.php" class="active">Reports</a></li>
                <li class="sidebar-item"><a href="profile.php">Profile</a></li>
            </nav>
        </aside>
        <div class="dashboard-content">
            <div class="content">
            <h1>Sales Reports</h1>
            <p class="subtitle">View and download your sales reports</p>

            <div class="section">
                <h2>Report Summary</h2>
                <div class="stat-row">
                    <div class="stat-box">
                        <span class="stat-label">Total Revenue</span>
                        <span class="stat-value" style="color: #2ecc71;">$<?php echo number_format($total_revenue, 2); ?></span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-label">Total Orders</span>
                        <span class="stat-value" style="color: #3498db;"><?php echo $total_orders; ?></span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-label">Average Order Value</span>
                        <span class="stat-value" style="color: #9b59b6;">$<?php echo number_format($average_order_value, 2); ?></span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-label">Total Transactions</span>
                        <span class="stat-value" style="color: #e67e22;"><?php echo count($farmer_orders); ?></span>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Download Report</h2>
                <p>Select your preferred format to download your complete sales report:</p>
                <div style="display: flex; gap: 15px; margin: 20px 0; flex-wrap: wrap;">
                    <a href="farmer-reports.php?download=csv" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                        <span>📊</span> Download as CSV
                    </a>
                    <a href="farmer-reports.php?download=pdf" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                        <span>📄</span> Download as Text
                    </a>
                </div>
            </div>

            <div class="section">
                <h2>Paid Orders Report (<?php echo $total_orders; ?> orders)</h2>
                <?php if (count($paid_orders) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Buyer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paid_orders as $o): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($o['order_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($o['buyer_name']); ?></td>
                                    <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                    <td><span class="badge badge-<?php echo $o['order_status']; ?>"><?php echo ucfirst($o['order_status']); ?></span></td>
                                    <td><span class="badge badge-<?php echo $o['payment_status']; ?>"><?php echo ucfirst($o['payment_status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                    <td><a href="farmer-order-details.php?id=<?php echo $o['id']; ?>" class="btn-small">Details</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; padding: 20px; background-color: #f8f9fa; border-radius: 4px;">
                        No paid orders found yet. Your completed orders will appear here.
                    </p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>All Orders (<?php echo count($farmer_orders); ?> orders)</h2>
                <?php if (count($farmer_orders) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Buyer</th>
                                <th>Amount</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($farmer_orders as $o): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($o['order_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($o['buyer_name']); ?></td>
                                    <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                                    <td><span class="badge badge-<?php echo $o['order_status']; ?>"><?php echo ucfirst($o['order_status']); ?></span></td>
                                    <td><span class="badge badge-<?php echo $o['payment_status']; ?>"><?php echo ucfirst($o['payment_status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No orders yet.</p>
                <?php endif; ?>
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
