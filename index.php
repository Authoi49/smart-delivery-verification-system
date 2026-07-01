<?php
session_start();
$base = '';
$pageTitle = 'Home';
include 'includes/header.php';
?>

<section class="hero">
    <h1>Smart Delivery Verification System</h1>
    <p>Shop with confidence. Every delivery is confirmed with a one-time OTP and a photo proof — no more disputes between riders and customers.</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn btn-primary">Get Started</a>
        <a href="login.php" class="btn btn-outline" style="background:#fff;">Login</a>
    <?php else: ?>
        <?php
            $role = $_SESSION['role'];
            $target = $role === 'admin' ? 'admin/dashboard.php' : ($role === 'rider' ? 'rider/dashboard.php' : 'customer/dashboard.php');
        ?>
        <a href="<?php echo $target; ?>" class="btn btn-primary" style="background:#fff; color: var(--primary-dark);">Go to Dashboard</a>
    <?php endif; ?>
</section>

<div class="container">
    <div class="grid">
        <div class="card">
            <h3>🛍️ For Customers</h3>
            <p class="text-muted mt-16">Browse products, place orders, and receive a secure OTP when your rider arrives. Confirm delivery only when you're satisfied.</p>
        </div>
        <div class="card">
            <h3>🛵 For Riders</h3>
            <p class="text-muted mt-16">View your assigned deliveries, enter the customer's OTP, and upload a photo proof to mark the order complete.</p>
        </div>
        <div class="card">
            <h3>🛠️ For Admins</h3>
            <p class="text-muted mt-16">Manage the product catalog, assign riders to pending orders, and monitor every delivery in real time.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
