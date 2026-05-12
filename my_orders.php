<?php
require_once 'config.php';
$pageTitle = 'My Orders';

if (!isLoggedIn() || isAdmin()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-shopping-bag"></i> My Orders</h1>
        <p>Track and view your order history</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($orders->num_rows === 0): ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>No orders yet</h3>
                <p>Start ordering from our delicious menu!</p>
                <a href="menu.php" class="btn btn-primary">View Menu</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <span class="order-date"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                        </div>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <div class="order-details">
                        <?php
                        $itemStmt = $conn->prepare("SELECT oi.*, mi.name FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?");
                        $itemStmt->bind_param("i", $order['id']);
                        $itemStmt->execute();
                        $orderItems = $itemStmt->get_result();
                        ?>
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($oi = $orderItems->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($oi['name']); ?></td>
                                    <td><?php echo $oi['quantity']; ?></td>
                                    <td><?php echo formatPrice($oi['unit_price']); ?></td>
                                    <td><?php echo formatPrice($oi['subtotal']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php $itemStmt->close(); ?>
                    </div>
                    <div class="order-footer">
                        <span class="order-type"><i class="fas fa-<?php echo $order['order_type'] === 'delivery' ? 'truck' : ($order['order_type'] === 'takeaway' ? 'shopping-bag' : 'chair'); ?>"></i> <?php echo ucfirst($order['order_type']); ?></span>
                        <span class="order-total">Total: <strong><?php echo formatPrice($order['total_amount']); ?></strong></span>
                    </div>
                    <?php if ($order['notes']): ?>
                        <div class="order-notes">
                            <small><i class="fas fa-sticky-note"></i> <?php echo htmlspecialchars($order['notes']); ?></small>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
