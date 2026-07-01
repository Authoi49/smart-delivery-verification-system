/**
 * cart.js
 * Client-side shopping cart logic, shared across dashboard.php and cart.php.
 * Cart data is stored in the browser's localStorage as JSON, keyed per
 * logged-in user isn't strictly necessary here since this is a single-user
 * browser session demo, but we still scope the key clearly.
 */

const SmartCart = (function () {
    const STORAGE_KEY = 'smart_delivery_cart';

    function getCart() {
        const data = localStorage.getItem(STORAGE_KEY);
        return data ? JSON.parse(data) : [];
    }

    function saveCart(cart) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    }

    function addItem(product) {
        const cart = getCart();
        const existing = cart.find(item => item.id === product.id);

        if (existing) {
            if (existing.qty + 1 > product.stock) {
                return false; // not enough stock
            }
            existing.qty += 1;
        } else {
            if (product.stock < 1) return false;
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                stock: product.stock,
                qty: 1
            });
        }
        saveCart(cart);
        return true;
    }

    function updateQty(productId, newQty) {
        const cart = getCart();
        const item = cart.find(i => i.id === productId);
        if (!item) return;

        if (newQty <= 0) {
            removeItem(productId);
            return;
        }
        if (newQty > item.stock) {
            newQty = item.stock;
        }
        item.qty = newQty;
        saveCart(cart);
    }

    function removeItem(productId) {
        let cart = getCart();
        cart = cart.filter(i => i.id !== productId);
        saveCart(cart);
    }

    function clearCart() {
        localStorage.removeItem(STORAGE_KEY);
    }

    function getItemCount() {
        return getCart().reduce((sum, item) => sum + item.qty, 0);
    }

    function getTotal() {
        return getCart().reduce((sum, item) => sum + (item.qty * item.price), 0);
    }

    return {
        getCart,
        addItem,
        updateQty,
        removeItem,
        clearCart,
        getItemCount,
        getTotal
    };
})();
