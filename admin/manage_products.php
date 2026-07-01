<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('admin');
$pageTitle = 'Manage Products';

$successMsg = $_SESSION['product_success'] ?? '';
unset($_SESSION['product_success']);

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

include '../includes/header.php';
?>

<div class="container">
    <div class="flex-between">
        <h1 class="page-title">Manage Products</h1>
        <a href="add_product.php" class="btn btn-primary">+ Add Product</a>
    </div>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <div class="table-wrap mt-16">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = $products->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../uploads/products/<?php echo htmlspecialchars($p['image']); ?>" onerror="this.src='https://placehold.co/50x50?text=No+Img'" style="width:50px;height:50px;object-fit:cover;border-radius:6px;"></td>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td>৳<?php echo number_format($p['price'], 2); ?></td>
                        <td><?php echo $p['stock']; ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $p['product_id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                            <a href="delete_product.php?id=<?php echo $p['product_id']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this product? This cannot be undone.');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
