<?php
require_once 'config.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    setFlash('error', 'Your cart is empty.');
    redirect('cart.php');
}

$orderType = sanitize($_POST['order_type'] ?? 'dine-in');
$deliveryAddress = sanitize($_POST['delivery_address'] ?? '');
$notes = sanitize($_POST['notes'] ?? '');
$userId = $_SESSION['user_id'];

// Calculate total
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

$stmt = $conn->prepare("SELECT id, price FROM menu_items WHERE id IN ($placeholders) AND is_available = 1");
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();

$totalAmount = 0;
$items = [];
while ($item = $result->fetch_assoc()) {
    $qty = $cart[$item['id']]['quantity'];
    $subtotal = $item['price'] * $qty;
    $totalAmount += $subtotal;
    $items[] = [
        'menu_item_id' => $item['id'],
        'quantity' => $qty,
        'unit_price' => $item['price'],
        'subtotal' => $subtotal
    ];
}
$stmt->close();

// Add service charge
$totalWithService = $totalAmount * 1.15;

// Begin transaction
$conn->begin_transaction();

try {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_type, delivery_address, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idsss", $userId, $totalWithService, $orderType, $deliveryAddress, $notes);
    $stmt->execute();
    $orderId = $conn->insert_id;
    $stmt->close();

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->bind_param("iiidd", $orderId, $item['menu_item_id'], $item['quantity'], $item['unit_price'], $item['subtotal']);
        $stmt->execute();
    }
    $stmt->close();

    $conn->commit();

    // Clear cart
    unset($_SESSION['cart']);
    setFlash('success', 'Order #' . $orderId . ' placed successfully! Total: ' . formatPrice($totalWithService));
    redirect('my_orders.php');

} catch (Exception $e) {
    $conn->rollback();
    setFlash('error', 'Failed to place order. Please try again.');
    redirect('cart.php');
}
?>
