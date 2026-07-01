<?php
/**
 * header.php
 * Shared top navigation. Include after session_start().
 * Uses $base (relative path prefix) so it works from root, /customer/, /rider/, /admin/.
 */
if (!isset($base)) { $base = ''; }
$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Smart Delivery' : 'Smart Delivery Verification System'; ?></title>
<link rel="stylesheet" href="<?php echo $base; ?>css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="<?php echo $base; ?>index.php" class="brand">📦 Smart Delivery</a>
    <div class="nav-links">
        <?php if ($loggedIn): ?>
            <?php if ($role === 'customer'): ?>
                <a href="<?php echo $base; ?>customer/dashboard.php">Shop</a>
                <a href="<?php echo $base; ?>customer/cart.php">Cart</a>
                <a href="<?php echo $base; ?>customer/orders.php">My Orders</a>
            <?php elseif ($role === 'rider'): ?>
                <a href="<?php echo $base; ?>rider/dashboard.php">Assigned Orders</a>
            <?php elseif ($role === 'admin'): ?>
                <a href="<?php echo $base; ?>admin/dashboard.php">Dashboard</a>
                <a href="<?php echo $base; ?>admin/manage_products.php">Products</a>
                <a href="<?php echo $base; ?>admin/manage_orders.php">Orders</a>
            <?php endif; ?>
            <span class="badge-role"><?php echo htmlspecialchars($role); ?></span>
            <span class="text-muted">Hi, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="<?php echo $base; ?>logout.php" class="btn btn-outline btn-sm">Logout</a>
        <?php else: ?>
            <a href="<?php echo $base; ?>login.php">Login</a>
            <a href="<?php echo $base; ?>register.php" class="btn btn-primary btn-sm">Register</a>
        <?php endif; ?>
    </div>
</nav>
