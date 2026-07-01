<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('customer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit();
}

$cartDataRaw = $_POST['cart_data'] ?? '';
$address = trim($_POST['delivery_address'] ?? '');
$customerId = current_user_id();

$cartItems = json_decode($cartDataRaw, true);

if (!is_array($cartItems) || count($cartItems) === 0 || $address === '') {
    $_SESSION['checkout_error'] = 'Your cart is empty or the delivery address is missing.';
    header("Location: cart.php");
    exit();
}

// ---- SERVER-SIDE REVALIDATION ----
// Never trust prices/stock sent from the browser. Re-fetch real data from DB.
$validatedItems = [];
$totalAmount = 0;
$stockError = '';

foreach ($cartItems as $item) {
    $productId = intval($item['id'] ?? 0);
    $qty = intval($item['qty'] ?? 0);
    if ($productId <= 0 || $qty <= 0) continue;

    $stmt = $conn->prepare("SELECT product_id, name, price, stock FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $res = $stmt->get_result();
    $product = $res->fetch_assoc();
    $stmt->close();

    if (!$product) continue;

    if ($qty > $product['stock']) {
        $stockError = "Not enough stock for " . $product['name'] . ".";
        break;
    }

    $validatedItems[] = [
        'product_id' => $product['product_id'],
        'price' => $product['price'],
        'qty' => $qty
    ];
    $totalAmount += $product['price'] * $qty;
}

if ($stockError !== '') {
    $_SESSION['checkout_error'] = $stockError;
    header("Location: cart.php");
    exit();
}

if (count($validatedItems) === 0) {
    $_SESSION['checkout_error'] = 'No valid items in cart.';
    header("Location: cart.php");
    exit();
}

// ---- Create order in a transaction ----
$conn->begin_transaction();
try {
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, delivery_address, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("ids", $customerId, $totalAmount, $address);
    $stmt->execute();
    $orderId = $conn->insert_id;
    $stmt->close();

    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");

    foreach ($validatedItems as $vi) {
        $itemStmt->bind_param("iiid", $orderId, $vi['product_id'], $vi['qty'], $vi['price']);
        $itemStmt->execute();

        $stockStmt->bind_param("ii", $vi['qty'], $vi['product_id']);
        $stockStmt->execute();
    }
    $itemStmt->close();
    $stockStmt->close();

    $conn->commit();

    $_SESSION['order_success'] = "Order #$orderId placed successfully!";
    header("Location: orders.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['checkout_error'] = 'Something went wrong while placing your order. Please try again.';
    header("Location: cart.php");
    exit();
}
