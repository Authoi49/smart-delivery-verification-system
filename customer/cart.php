<?php
session_start();
$base = '../';
require_once '../includes/auth.php';
require_role('customer');
$pageTitle = 'My Cart';
include '../includes/header.php';
?>

<?php
$checkoutError = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);
?>

<div class="container">
    <h1 class="page-title">My Cart</h1>

    <?php if ($checkoutError): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($checkoutError); ?></div>
    <?php endif; ?>

    <div id="cartContainer"></div>

    <div id="emptyCartMsg" class="card text-center hidden">
        <p class="text-muted">Your cart is empty.</p>
        <a href="dashboard.php" class="btn btn-primary mt-16">Browse Products</a>
    </div>

    <div id="cartSummaryBox" class="cart-summary hidden">
        <div class="total-row">
            <span>Total</span>
            <span id="cartTotal">৳0.00</span>
        </div>
        <form action="checkout.php" method="POST" id="checkoutForm">
            <input type="hidden" name="cart_data" id="cartDataInput">
            <div class="form-group">
                <label for="delivery_address">Delivery Address</label>
                <textarea name="delivery_address" id="delivery_address" rows="2" required placeholder="Enter your full delivery address"></textarea>
            </div>
            <button type="submit" class="btn btn-success btn-block">Proceed to Checkout</button>
        </form>
    </div>
</div>

<script src="../js/cart.js"></script>
<script>
    function renderCart() {
        const cart = SmartCart.getCart();
        const container = document.getElementById('cartContainer');
        const emptyMsg = document.getElementById('emptyCartMsg');
        const summaryBox = document.getElementById('cartSummaryBox');

        if (cart.length === 0) {
            container.innerHTML = '';
            emptyMsg.classList.remove('hidden');
            summaryBox.classList.add('hidden');
            return;
        }

        emptyMsg.classList.add('hidden');
        summaryBox.classList.remove('hidden');

        container.innerHTML = cart.map(item => `
            <div class="cart-item" data-id="${item.id}">
                <div class="cart-item-info">
                    <img src="../uploads/products/${item.image}" onerror="this.src='https://placehold.co/60x60?text=No+Image'">
                    <div>
                        <h4>${escapeHtml(item.name)}</h4>
                        <span class="text-muted">৳${item.price.toFixed(2)} each</span>
                    </div>
                </div>
                <div class="flex" style="gap:18px; align-items:center;">
                    <div class="qty-controls">
                        <button type="button" class="decrease-btn">−</button>
                        <span class="qty-display">${item.qty}</span>
                        <button type="button" class="increase-btn">+</button>
                    </div>
                    <strong>৳${(item.qty * item.price).toFixed(2)}</strong>
                    <button type="button" class="btn btn-danger btn-sm remove-btn">Remove</button>
                </div>
            </div>
        `).join('');

        document.getElementById('cartTotal').textContent = '৳' + SmartCart.getTotal().toFixed(2);

        // Wire up buttons
        container.querySelectorAll('.cart-item').forEach(row => {
            const id = row.dataset.id;
            row.querySelector('.increase-btn').addEventListener('click', () => {
                const item = SmartCart.getCart().find(i => i.id === id);
                SmartCart.updateQty(id, item.qty + 1);
                renderCart();
            });
            row.querySelector('.decrease-btn').addEventListener('click', () => {
                const item = SmartCart.getCart().find(i => i.id === id);
                SmartCart.updateQty(id, item.qty - 1);
                renderCart();
            });
            row.querySelector('.remove-btn').addEventListener('click', () => {
                SmartCart.removeItem(id);
                renderCart();
            });
        });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    document.getElementById('checkoutForm').addEventListener('submit', function (e) {
        const cart = SmartCart.getCart();
        if (cart.length === 0) {
            e.preventDefault();
            return;
        }
        document.getElementById('cartDataInput').value = JSON.stringify(cart);
    });

    renderCart();
</script>

<?php include '../includes/footer.php'; ?>
