<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('rider');
$pageTitle = 'Share Live Location';

$riderId = current_user_id();
$orderId = intval($_GET['order_id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND rider_id = ?");
$stmt->bind_param("ii", $orderId, $riderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: dashboard.php");
    exit();
}

if ($order['status'] !== 'Assigned') {
    $_SESSION['info_msg'] = 'Location sharing is only available while a delivery is in progress.';
    header("Location: dashboard.php");
    exit();
}

include '../includes/header.php';
?>

<div class="container">
    <div class="form-box" style="max-width: 480px;">
        <h2>Share Live Location — Order #<?php echo $order['order_id']; ?></h2>
        <p class="text-muted">Keep this page open while you're on your way. Your location updates automatically every few seconds so the customer can track you.</p>

        <div id="statusBox" class="alert alert-info mt-16">Waiting for GPS permission...</div>

        <div class="stat-card mt-16">
            <div class="label">Last sent</div>
            <div class="num" id="lastSentTime" style="font-size:1.1rem;">—</div>
        </div>

        <a href="dashboard.php" class="btn btn-outline btn-block mt-24">Back to Dashboard</a>
        <a href="verify_delivery.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-success btn-block mt-16">I've Arrived — Verify Delivery</a>
    </div>
</div>

<script>
    const ORDER_ID = <?php echo $order['order_id']; ?>;
    const UPDATE_URL = 'update_location.php';
    const statusBox = document.getElementById('statusBox');
    const lastSentTime = document.getElementById('lastSentTime');

    if (!navigator.geolocation) {
        statusBox.textContent = 'Geolocation is not supported by this browser.';
        statusBox.className = 'alert alert-error mt-16';
    } else {
        navigator.geolocation.watchPosition(
            onLocationSuccess,
            onLocationError,
            {
                enableHighAccuracy: true,
                maximumAge: 5000,
                timeout: 10000
            }
        );
    }

    function onLocationSuccess(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        fetch(UPDATE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: ORDER_ID, lat: lat, lng: lng })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                statusBox.textContent = '✅ Sharing your live location...';
                statusBox.className = 'alert alert-success mt-16';
                lastSentTime.textContent = new Date().toLocaleTimeString();
            } else {
                statusBox.textContent = 'Error: ' + (data.error || 'Could not update location.');
                statusBox.className = 'alert alert-error mt-16';
            }
        })
        .catch(() => {
            statusBox.textContent = 'Network error while sending location. Retrying...';
            statusBox.className = 'alert alert-error mt-16';
        });
    }

    function onLocationError(error) {
        statusBox.textContent = 'Location permission denied or unavailable. Please allow location access.';
        statusBox.className = 'alert alert-error mt-16';
    }
</script>

<?php include '../includes/footer.php'; ?>
