<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_orders.php");
    exit();
}

$orderId = intval($_POST['order_id'] ?? 0);
$riderId = intval($_POST['rider_id'] ?? 0);

if ($orderId > 0 && $riderId > 0) {
    // Generate a fresh 6-digit OTP for this delivery
    $otp = generate_otp();

    $stmt = $conn->prepare("UPDATE orders SET rider_id = ?, status = 'Assigned', otp_code = ? WHERE order_id = ? AND status = 'Pending'");
    $stmt->bind_param("isi", $riderId, $otp, $orderId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['order_assign_success'] = "Rider assigned to Order #$orderId. OTP generated and visible to the customer.";
}

header("Location: manage_orders.php");
exit();
