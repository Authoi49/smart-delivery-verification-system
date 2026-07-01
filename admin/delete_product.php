<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('admin');

$productId = intval($_GET['id'] ?? 0);

if ($productId > 0) {
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->close();
    $_SESSION['product_success'] = 'Product deleted successfully.';
}

header("Location: manage_products.php");
exit();
