<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
$status = sanitize($_POST['status'] ?? '');

$validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];

if (!in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status.']);
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $orderId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order status updated to ' . ucfirst($status) . '.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
}
$stmt->close();
?>
