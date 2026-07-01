<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('rider');
$pageTitle = 'Verify Delivery';

$riderId = current_user_id();
$orderId = intval($_GET['order_id'] ?? 0);
$error = '';

// Fetch the order, making sure it belongs to this rider and is still "Assigned"
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND rider_id = ?");
$stmt->bind_param("ii", $orderId, $riderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: dashboard.php");
    exit();
}

if ($order['status'] === 'Delivered') {
    $_SESSION['info_msg'] = 'This order has already been delivered.';
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = trim($_POST['otp_code'] ?? '');

    if ($enteredOtp === '') {
        $error = 'Please enter the OTP provided by the customer.';
    } elseif ($enteredOtp !== $order['otp_code']) {
        $error = 'Incorrect OTP. Please check with the customer and try again.';
    } elseif (!isset($_FILES['proof_photo']) || $_FILES['proof_photo']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a delivery proof photo.';
    } else {
        // ---- Handle file upload ----
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileType = mime_content_type($_FILES['proof_photo']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $error = 'Only JPG, PNG, or WEBP images are allowed.';
        } elseif ($_FILES['proof_photo']['size'] > 5 * 1024 * 1024) {
            $error = 'Image must be smaller than 5MB.';
        } else {
            $ext = pathinfo($_FILES['proof_photo']['name'], PATHINFO_EXTENSION);
            $newFileName = 'proof_order' . $orderId . '_' . time() . '.' . $ext;
            $destination = '../uploads/proofs/' . $newFileName;

            if (move_uploaded_file($_FILES['proof_photo']['tmp_name'], $destination)) {
                $stmt = $conn->prepare("UPDATE orders SET status = 'Delivered', proof_photo = ?, delivered_at = NOW() WHERE order_id = ?");
                $stmt->bind_param("si", $newFileName, $orderId);
                $stmt->execute();
                $stmt->close();

                $_SESSION['delivery_success'] = "Order #$orderId marked as delivered successfully!";
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Failed to upload the photo. Please try again.';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="form-box" style="max-width: 480px;">
        <h2>Verify Delivery — Order #<?php echo $order['order_id']; ?></h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <p class="text-muted">Ask the customer for their 6-digit OTP and enter it below.</p>

        <form method="POST" action="verify_delivery.php?order_id=<?php echo $orderId; ?>" enctype="multipart/form-data" id="verifyForm">
            <div class="form-group">
                <label>Enter OTP</label>
                <div class="otp-inputs" id="otpInputs">
                    <input type="text" maxlength="1" inputmode="numeric" class="otp-box">
                    <input type="text" maxlength="1" inputmode="numeric" class="otp-box">
                    <input type="text" maxlength="1" inputmode="numeric" class="otp-box">
                    <input type="text" maxlength="1" inputmode="numeric" class="otp-box">
                    <input type="text" maxlength="1" inputmode="numeric" class="otp-box">
                    <input type="text" maxlength="1" inputmode="numeric" class="otp-box">
                </div>
                <input type="hidden" name="otp_code" id="otpHidden">
            </div>

            <div class="form-group">
                <label for="proof_photo">Delivery Proof Photo</label>
                <input type="file" name="proof_photo" id="proof_photo" accept="image/*" required>
                <img id="photoPreview" class="proof-photo mt-16 hidden">
            </div>

            <button type="submit" class="btn btn-success btn-block">Confirm Delivery</button>
            <a href="dashboard.php" class="btn btn-outline btn-block mt-16">Cancel</a>
        </form>
    </div>
</div>

<script src="../js/otp.js"></script>
<?php include '../includes/footer.php'; ?>
