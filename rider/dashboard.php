<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('rider');
$pageTitle = 'Assigned Orders';

$riderId = current_user_id();

$stmt = $conn->prepare("
    SELECT o.*, u.full_name AS customer_name, u.phone AS customer_phone
    FROM orders o
    JOIN users u ON o.customer_id = u.user_id
    WHERE o.rider_id = ?
    ORDER BY
        CASE o.status WHEN 'Assigned' THEN 0 WHEN 'Delivered' THEN 1 END,
        o.created_at DESC
");
$stmt->bind_param("i", $riderId);
$stmt->execute();
$orders = $stmt->get_result();

$successMsg = $_SESSION['delivery_success'] ?? ($_SESSION['info_msg'] ?? '');
unset($_SESSION['delivery_success'], $_SESSION['info_msg']);

include '../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">My Assigned Deliveries</h1>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>


    <?php if ($orders->num_rows === 0): ?>
        <div class="card text-center">
            <p class="text-muted">No deliveries assigned to you yet. Check back soon.</p>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="card">
                    <div class="flex-between">
                        <h3>Order #<?php echo $order['order_id']; ?></h3>
                        <span class="status status-<?php echo $order['status']; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                    </div>
                    <p class="mt-16"><b>Customer:</b> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><b>Phone:</b> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    <p><b>Address:</b> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                    <p><b>Amount to Collect:</b> ৳<?php echo number_format($order['total_amount'], 2); ?></p>

                    <?php if ($order['status'] === 'Assigned'): ?>
                        <a href="share_location.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary btn-block mt-16">📍 Share Live Location</a>
                        <a href="verify_delivery.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-success btn-block mt-16">Verify & Complete Delivery</a>
                    <?php else: ?>
                        <p class="text-muted mt-16">Delivered on <?php echo date('d M Y, h:i A', strtotime($order['delivered_at'])); ?></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
