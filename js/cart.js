/* ============================================
   Shopping Cart - AJAX/Fetch (Asynchronous)
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

    var siteUrl = document.querySelector('link[rel="stylesheet"]').href;
    var baseUrl = siteUrl.substring(0, siteUrl.indexOf('/css/'));

    // ---- Add to Cart buttons ----
    var addButtons = document.querySelectorAll('.add-to-cart');
    addButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var itemId = this.getAttribute('data-id');
            addToCart(itemId, this);
        });
    });

    function addToCart(itemId, button) {
        var originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        var formData = new FormData();
        formData.append('action', 'add');
        formData.append('item_id', itemId);

        // Asynchronous fetch for cart operations
        fetch(baseUrl + '/api/cart.php', {
            method: 'POST',
            body: formData
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                updateCartCount(data.cartCount);
                button.innerHTML = '<i class="fas fa-check"></i> Added!';
                button.classList.add('btn-success');
                button.classList.remove('btn-primary');

                setTimeout(function () {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                    button.disabled = false;
                }, 1500);

                showToast(data.message);
            } else {
                button.innerHTML = originalText;
                button.disabled = false;
                showToast(data.message, 'error');
            }
        })
        .catch(function () {
            button.innerHTML = originalText;
            button.disabled = false;
            showToast('Failed to add item. Please try again.', 'error');
        });
    }

    // ---- Cart Page: Quantity Controls ----
    var plusBtns = document.querySelectorAll('.qty-plus');
    var minusBtns = document.querySelectorAll('.qty-minus');
    var removeBtns = document.querySelectorAll('.remove-item');

    plusBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var itemId = this.getAttribute('data-id');
            var row = this.closest('tr');
            var qtyEl = row.querySelector('.qty-value');
            var newQty = parseInt(qtyEl.textContent) + 1;
            updateCartItem(itemId, newQty);
        });
    });

    minusBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var itemId = this.getAttribute('data-id');
            var row = this.closest('tr');
            var qtyEl = row.querySelector('.qty-value');
            var newQty = parseInt(qtyEl.textContent) - 1;
            if (newQty < 1) {
                removeCartItem(itemId);
            } else {
                updateCartItem(itemId, newQty);
            }
        });
    });

    removeBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var itemId = this.getAttribute('data-id');
            removeCartItem(itemId);
        });
    });

    function updateCartItem(itemId, quantity) {
        var formData = new FormData();
        formData.append('action', 'update');
        formData.append('item_id', itemId);
        formData.append('quantity', quantity);

        fetch(baseUrl + '/api/cart.php', {
            method: 'POST',
            body: formData
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                location.reload();
            }
        })
        .catch(function () {
            showToast('Failed to update cart.', 'error');
        });
    }

    function removeCartItem(itemId) {
        var formData = new FormData();
        formData.append('action', 'remove');
        formData.append('item_id', itemId);

        fetch(baseUrl + '/api/cart.php', {
            method: 'POST',
            body: formData
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                location.reload();
            }
        })
        .catch(function () {
            showToast('Failed to remove item.', 'error');
        });
    }

    // ---- Update Cart Count ----
    function updateCartCount(count) {
        var cartCountEl = document.getElementById('cartCount');
        if (cartCountEl) {
            cartCountEl.textContent = count;
        }
    }

    // ---- Toast Notification ----
    function showToast(message, type) {
        type = type || 'success';
        var toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;bottom:20px;right:20px;padding:12px 24px;border-radius:8px;color:#fff;font-size:14px;font-weight:500;z-index:9999;animation:fadeIn 0.3s ease;box-shadow:0 5px 15px rgba(0,0,0,0.2);';
        toast.style.background = type === 'success' ? '#27ae60' : '#e74c3c';
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(function () { toast.remove(); }, 300);
        }, 3000);
    }
});
