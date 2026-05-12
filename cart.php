<?php
require_once 'config.php';
$pageTitle = 'Shopping Cart';

if (!isLoggedIn() || isAdmin()) {
    redirect('login.php');
}

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$totalAmount = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    
    $stmt = $conn->prepare("SELECT id, name, price, is_available FROM menu_items WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($item = $result->fetch_assoc()) {
        $qty = $cart[$item['id']]['quantity'];
        $subtotal = $item['price'] * $qty;
        $totalAmount += $subtotal;
        $cartItems[] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $qty,
            'subtotal' => $subtotal,
            'available' => $item['is_available']
        ];
    }
    $stmt->close();
}

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-shopping-cart"></i> Your Cart</h1>
        <p>Review your order before checkout</p>
    </div>
</section>

<section class="section cart-section">
    <div class="container">
        <?php if (empty($cartItems)): ?>
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Browse our menu and add some delicious items!</p>
                <a href="menu.php" class="btn btn-primary">View Menu</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <?php foreach ($cartItems as $item): ?>
                            <tr data-id="<?php echo $item['id']; ?>">
                                <td class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td>
                                    <div class="quantity-control">
                                        <button class="qty-btn qty-minus" data-id="<?php echo $item['id']; ?>">-</button>
                                        <span class="qty-value"><?php echo $item['quantity']; ?></span>
                                        <button class="qty-btn qty-plus" data-id="<?php echo $item['id']; ?>">+</button>
                                    </div>
                                </td>
                                <td class="item-subtotal"><?php echo formatPrice($item['subtotal']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger remove-item" data-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="cartSubtotal"><?php echo formatPrice($totalAmount); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Service Charge (15%):</span>
                        <span id="cartService"><?php echo formatPrice($totalAmount * 0.15); ?></span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total:</span>
                        <span id="cartTotal"><?php echo formatPrice($totalAmount * 1.15); ?></span>
                    </div>

                    <form action="checkout.php" method="POST" class="checkout-form">
                        <div class="form-group">
                            <label for="order_type">Order Type</label>
                            <select id="order_type" name="order_type" required>
                                <option value="dine-in">Dine In</option>
                                <option value="takeaway">Takeaway</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>
                        <div class="form-group" id="deliveryAddressGroup" style="display:none;">
                            <label for="delivery_address">Delivery Address</label>
                            <textarea id="delivery_address" name="delivery_address" rows="2" placeholder="Enter delivery address"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="notes">Special Notes</label>
                            <textarea id="notes" name="notes" rows="2" placeholder="Any special requests?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-check"></i> Place Order
                        </button>
                    </form>
                    <a href="menu.php" class="btn btn-outline btn-block mt-1">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="<?php echo SITE_URL; ?>/js/cart.js"></script>

<?php include 'includes/footer.php'; ?>
