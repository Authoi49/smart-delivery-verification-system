<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('customer');
$pageTitle = 'My Orders';

$successMsg = $_SESSION['order_success'] ?? '';
unset($_SESSION['order_success']);

$customerId = current_user_id();

$stmt = $conn->prepare("
    SELECT o.*, u.full_name AS rider_name, u.phone AS rider_phone
    FROM orders o
    LEFT JOIN users u ON o.rider_id = u.user_id
    WHERE o.customer_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$orders = $stmt->get_result();

include '../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">My Orders</h1>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
        <script>
            // Order placed successfully -> clear the local cart now that it's saved server-side.
        </script>
    <?php endif; ?>

    <?php if ($orders->num_rows === 0): ?>
        <div class="card text-center">
            <p class="text-muted">You haven't placed any orders yet.</p>
            <a href="dashboard.php" class="btn btn-primary mt-16">Start Shopping</a>
        </div>
    <?php else: ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="card mt-16">
                <div class="flex-between">
                    <h3>Order #<?php echo $order['order_id']; ?></h3>
                    <span class="status status-<?php echo $order['status']; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                </div>
                <p class="text-muted mt-16">Placed on <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                <p class="mt-16"><b>Delivery Address:</b> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                <p><b>Total:</b> ৳<?php echo number_format($order['total_amount'], 2); ?></p>

                <?php if ($order['status'] === 'Assigned'): ?>
                    <p><b>Rider:</b> <?php echo htmlspecialchars($order['rider_name']); ?> (<?php echo htmlspecialchars($order['rider_phone']); ?>)</p>
                    <div class="alert alert-info mt-16">
                        Share this OTP with your rider <b>only when the package is physically in your hands</b>:
                        <h2 style="text-align:center; letter-spacing:6px; margin-top:8px;"><?php echo htmlspecialchars($order['otp_code']); ?></h2>
                    </div>
                    <a href="track_order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary btn-block mt-16">📍 Track Rider on Map</a>
                <?php elseif ($order['status'] === 'Delivered'): ?>
                    <p class="text-muted">Delivered on <?php echo date('d M Y, h:i A', strtotime($order['delivered_at'])); ?></p>
                    <?php if ($order['proof_photo']): ?>
                        <p class="mt-16"><b>Delivery Proof:</b></p>
                        <img src="../uploads/proofs/<?php echo htmlspecialchars($order['proof_photo']); ?>" class="proof-photo">
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted mt-16">Waiting for an admin to assign a rider to your order.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<script src="../js/cart.js"></script>
<script>
    <?php if ($successMsg): ?>
    // Clear the cart in localStorage after a successful order placement
    SmartCart.clearCart();
    <?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
