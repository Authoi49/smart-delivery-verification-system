<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('customer');
$pageTitle = 'Track Order';

$customerId = current_user_id();
$orderId = intval($_GET['order_id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$stmt->bind_param("ii", $orderId, $customerId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: orders.php");
    exit();
}

include '../includes/header.php';
?>

<!-- Leaflet CSS (free, no API key, no billing - OpenStreetMap) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

<div class="container">
    <h1 class="page-title">Track Order #<?php echo $order['order_id']; ?></h1>

    <?php if ($order['status'] !== 'Assigned'): ?>
        <div class="alert alert-info">
            Live tracking is only available while your order is out for delivery.
            Current status: <span class="status status-<?php echo $order['status']; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
        </div>
        <a href="orders.php" class="btn btn-outline mt-16">Back to My Orders</a>
    <?php else: ?>
        <div id="mapStatus" class="alert alert-info">Waiting for your rider's location...</div>
        <div id="map" style="width:100%; height:450px; border-radius:10px; border:1px solid var(--border);"></div>
        <p class="text-muted mt-16" id="lastUpdated"></p>
        <a href="orders.php" class="btn btn-outline mt-16">Back to My Orders</a>
    <?php endif; ?>
</div>

<?php if ($order['status'] === 'Assigned'): ?>
<!-- Leaflet JS (free, no API key, no billing - OpenStreetMap) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
    const ORDER_ID = <?php echo $order['order_id']; ?>;
    const POLL_URL = 'get_location.php?order_id=' + ORDER_ID;
    const mapStatus = document.getElementById('mapStatus');
    const lastUpdated = document.getElementById('lastUpdated');

    let map, marker;
    const DEFAULT_CENTER = [24.3745, 88.6042]; // Rajshahi - replaced once rider location arrives

    function initMap() {
        map = L.map('map').setView(DEFAULT_CENTER, 13);

        // OpenStreetMap tiles - completely free, no key required
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    }

    function pollLocation() {
        fetch(POLL_URL)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    mapStatus.textContent = 'Error: ' + (data.error || 'Could not fetch location.');
                    mapStatus.className = 'alert alert-error';
                    return;
                }

                if (data.status !== 'Assigned') {
                    mapStatus.textContent = 'Your order status has changed to: ' + data.status;
                    mapStatus.className = 'alert alert-success';
                    return; // stop polling - order is no longer in transit
                }

                if (!data.has_location) {
                    mapStatus.textContent = 'Waiting for your rider to start sharing their location...';
                    mapStatus.className = 'alert alert-info';
                } else {
                    mapStatus.textContent = '✅ Live tracking active';
                    mapStatus.className = 'alert alert-success';

                    const pos = [data.lat, data.lng];

                    if (!marker) {
                        marker = L.marker(pos).addTo(map);
                        marker.bindPopup('Your rider is here').openPopup();
                    } else {
                        marker.setLatLng(pos);
                    }
                    map.setView(pos, map.getZoom());

                    if (data.updated_at) {
                        lastUpdated.textContent = 'Last updated: ' + new Date(data.updated_at.replace(' ', 'T')).toLocaleTimeString();
                    }
                }

                // Keep polling every 5 seconds as long as order is still Assigned
                setTimeout(pollLocation, 5000);
            })
            .catch(() => {
                mapStatus.textContent = 'Network error while checking location. Retrying...';
                mapStatus.className = 'alert alert-error';
                setTimeout(pollLocation, 5000);
            });
    }

    window.addEventListener('load', function () {
        initMap();
        pollLocation();
    });
</script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
