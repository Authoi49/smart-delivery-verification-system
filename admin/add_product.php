<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('admin');
$pageTitle = 'Add Product';

$errors = [];
$old = ['name' => '', 'description' => '', 'price' => '', 'stock' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = trim($_POST['name'] ?? '');
    $old['description'] = trim($_POST['description'] ?? '');
    $old['price'] = trim($_POST['price'] ?? '');
    $old['stock'] = trim($_POST['stock'] ?? '');

    if ($old['name'] === '') $errors['name'] = 'Product name is required.';
    if (!is_numeric($old['price']) || floatval($old['price']) <= 0) $errors['price'] = 'Enter a valid price.';
    if (!ctype_digit($old['stock']) && $old['stock'] !== '0') $errors['stock'] = 'Enter a valid stock quantity.';

    $imageName = 'no-image.png';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors['image'] = 'Only JPG, PNG, or WEBP images are allowed.';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $destination = '../uploads/products/' . $imageName;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $errors['image'] = 'Failed to upload image.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $old['name'], $old['description'], $old['price'], $imageName, $old['stock']);
        $stmt->execute();
        $stmt->close();

        $_SESSION['product_success'] = 'Product added successfully!';
        header("Location: manage_products.php");
        exit();
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="form-box">
        <h2>Add New Product</h2>

        <form method="POST" action="add_product.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($old['name']); ?>">
                <?php if (!empty($errors['name'])): ?><div class="form-error"><?php echo $errors['name']; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($old['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price (৳)</label>
                <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($old['price']); ?>">
                <?php if (!empty($errors['price'])): ?><div class="form-error"><?php echo $errors['price']; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="stock">Stock Quantity</label>
                <input type="text" id="stock" name="stock" value="<?php echo htmlspecialchars($old['stock']); ?>">
                <?php if (!empty($errors['stock'])): ?><div class="form-error"><?php echo $errors['stock']; ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*">
                <?php if (!empty($errors['image'])): ?><div class="form-error"><?php echo $errors['image']; ?></div><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Add Product</button>
            <a href="manage_products.php" class="btn btn-outline btn-block mt-16">Cancel</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
