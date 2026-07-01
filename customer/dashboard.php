<?php
session_start();
$base = '../';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_role('customer');

$pageTitle = 'Shop';
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

include '../includes/header.php';
?>

<div class="container">
    <div class="flex-between">
        <h1 class="page-title">Browse Products</h1>
        <a href="cart.php" class="btn btn-primary">🛒 View Cart (<span id="cartCount">0</span>)</a>
    </div>

    <div id="alertBox"></div>

    <div class="grid">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="card product-card">
                <img src="../uploads/products/<?php echo htmlspecialchars($product['image']); ?>"
                     onerror="this.src='https://placehold.co/300x200?text=No+Image'"
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="desc"><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="price">৳<?php echo number_format($product['price'], 2); ?></div>
                <button class="btn btn-primary btn-block add-to-cart-btn"
                        data-id="<?php echo $product['product_id']; ?>"
                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
                        data-price="<?php echo $product['price']; ?>"
                        data-image="<?php echo htmlspecialchars($product['image']); ?>"
                        data-stock="<?php echo $product['stock']; ?>">
                    Add to Cart
                </button>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="../js/cart.js"></script>
<script>
    // Wire up "Add to Cart" buttons on this page to the shared cart.js logic
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const product = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                image: this.dataset.image,
                stock: parseInt(this.dataset.stock)
            };
            const added = SmartCart.addItem(product);
            const alertBox = document.getElementById('alertBox');
            if (added) {
                alertBox.innerHTML = `<div class="alert alert-success">${product.name} added to cart!</div>`;
            } else {
                alertBox.innerHTML = `<div class="alert alert-error">Sorry, no more stock available for ${product.name}.</div>`;
            }
            setTimeout(() => { alertBox.innerHTML = ''; }, 2500);
            updateCartCount();
        });
    });
    updateCartCount();
    function updateCartCount() {
        document.getElementById('cartCount').textContent = SmartCart.getItemCount();
    }
</script>

<?php include '../includes/footer.php'; ?>
