<?php
/**
 * update_location.php
 * Called repeatedly (every few seconds) by the rider's browser via fetch()
 * while they have an active delivery open. Stores their latest GPS
 * coordinates against the order so the customer's map can poll and show it.
 *
 * Returns JSON. This is an API endpoint, not a page - no HTML/header here.
 */

session_start();
header('Content-Type: application/json');

require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

// Must be logged in as a rider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'rider') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not authorized.']);
    exit();
}

$riderId = current_user_id();

// Read JSON body sent by fetch()
$input = json_decode(file_get_contents('php://input'), true);

$orderId = intval($input['order_id'] ?? 0);
$lat = $input['lat'] ?? null;
$lng = $input['lng'] ?? null;

if ($orderId <= 0 || $lat === null || $lng === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing order_id, lat, or lng.']);
    exit();
}

// Validate latitude/longitude ranges
if (!is_numeric($lat) || !is_numeric($lng) || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid coordinates.']);
    exit();
}

// Make sure this order actually belongs to this rider and is still active (Assigned)
$stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND rider_id = ? AND status = 'Assigned'");
$stmt->bind_param("ii", $orderId, $riderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Order not found, not yours, or already delivered.']);
    exit();
}
$stmt->close();

// Save the rider's current location + timestamp
$stmt = $conn->prepare("UPDATE orders SET rider_lat = ?, rider_lng = ?, location_updated_at = NOW() WHERE order_id = ?");
$stmt->bind_param("ddi", $lat, $lng, $orderId);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
