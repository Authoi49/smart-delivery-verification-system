<?php
/**
 * get_location.php
 * Called repeatedly (every few seconds) by the customer's browser via fetch()
 * to retrieve the rider's latest position for a specific order.
 *
 * Returns JSON. This is an API endpoint, not a page - no HTML/header here.
 */

session_start();
header('Content-Type: application/json');

require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

// Must be logged in as a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not authorized.']);
    exit();
}

$customerId = current_user_id();
$orderId = intval($_GET['order_id'] ?? 0);

if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing order_id.']);
    exit();
}

// Make sure this order belongs to the logged-in customer
$stmt = $conn->prepare("
    SELECT status, rider_lat, rider_lng, location_updated_at
    FROM orders
    WHERE order_id = ? AND customer_id = ?
");
$stmt->bind_param("ii", $orderId, $customerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Order not found.']);
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

if ($order['status'] !== 'Assigned' || $order['rider_lat'] === null) {
    echo json_encode([
        'success' => true,
        'has_location' => false,
        'status' => $order['status']
    ]);
    exit();
}

echo json_encode([
    'success' => true,
    'has_location' => true,
    'status' => $order['status'],
    'lat' => floatval($order['rider_lat']),
    'lng' => floatval($order['rider_lng']),
    'updated_at' => $order['location_updated_at']
]);
