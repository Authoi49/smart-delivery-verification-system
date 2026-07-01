<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('admin');
$pageTitle = 'Admin Dashboard';

$totalProducts = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$totalOrders = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$pendingOrders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status = 'Pending'")->fetch_assoc()['c'];
$assignedOrders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status = 'Assigned'")->fetch_assoc()['c'];
$deliveredOrders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status = 'Delivered'")->fetch_assoc()['c'];
$totalCustomers = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'customer'")->fetch_assoc()['c'];
$totalRiders = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'rider'")->fetch_assoc()['c'];

include '../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Admin Dashboard</h1>

    <div class="stats-row">
        <div class="stat-card"><div class="num"><?php echo $totalProducts; ?></div><div class="label">Total Products</div></div>
        <div class="stat-card"><div class="num"><?php echo $totalOrders; ?></div><div class="label">Total Orders</div></div>
        <div class="stat-card"><div class="num"><?php echo $pendingOrders; ?></div><div class="label">Pending Orders</div></div>
        <div class="stat-card"><div class="num"><?php echo $assignedOrders; ?></div><div class="label">Assigned (In Transit)</div></div>
        <div class="stat-card"><div class="num"><?php echo $deliveredOrders; ?></div><div class="label">Delivered Orders</div></div>
        <div class="stat-card"><div class="num"><?php echo $totalCustomers; ?></div><div class="label">Customers</div></div>
        <div class="stat-card"><div class="num"><?php echo $totalRiders; ?></div><div class="label">Riders</div></div>
    </div>

    <div class="grid">
        <a href="manage_products.php" class="card">
            <h3>🛍️ Manage Products</h3>
            <p class="text-muted mt-16">Add, edit, or delete products in the catalog.</p>
        </a>
        <a href="manage_orders.php" class="card">
            <h3>📦 Manage Orders</h3>
            <p class="text-muted mt-16">Assign riders and monitor delivery status.</p>
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
