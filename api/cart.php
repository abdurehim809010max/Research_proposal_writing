<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn() || isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Please login to use cart.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$itemId = (int)($_POST['item_id'] ?? $_GET['item_id'] ?? 0);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    case 'add':
        // Verify item exists and is available
        $stmt = $conn->prepare("SELECT id, name, price FROM menu_items WHERE id = ? AND is_available = 1");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$item) {
            echo json_encode(['success' => false, 'message' => 'Item not available.']);
            exit;
        }

        if (isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId]['quantity']++;
        } else {
            $_SESSION['cart'][$itemId] = ['quantity' => 1];
        }

        echo json_encode([
            'success' => true,
            'message' => htmlspecialchars($item['name']) . ' added to cart!',
            'cartCount' => array_sum(array_column($_SESSION['cart'], 'quantity'))
        ]);
        break;

    case 'update':
        $quantity = (int)($_POST['quantity'] ?? 1);
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$itemId]);
        } else {
            $_SESSION['cart'][$itemId]['quantity'] = min($quantity, 10);
        }
        echo json_encode([
            'success' => true,
            'cartCount' => array_sum(array_column($_SESSION['cart'], 'quantity'))
        ]);
        break;

    case 'remove':
        unset($_SESSION['cart'][$itemId]);
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cartCount' => array_sum(array_column($_SESSION['cart'], 'quantity'))
        ]);
        break;

    case 'count':
        echo json_encode([
            'success' => true,
            'cartCount' => array_sum(array_column($_SESSION['cart'], 'quantity'))
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
?>
