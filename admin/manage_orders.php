<?php
require_once '../config.php';
$pageTitle = 'Manage Orders';

// Handle status update (non-AJAX fallback)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    if ($stmt->execute()) {
        setFlash('success', 'Order #' . $orderId . ' status updated to ' . ucfirst($status) . '.');
    }
    $stmt->close();
    redirect('manage_orders.php');
}

// Filter
$statusFilter = sanitize($_GET['status'] ?? '');
$searchQuery = sanitize($_GET['search'] ?? '');

$query = "SELECT o.*, u.full_name, u.email, u.phone FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
$params = [];
$types = '';

if (!empty($statusFilter)) {
    $query .= " AND o.status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}
if (!empty($searchQuery)) {
    $query .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR o.id = ?)";
    $searchLike = "%$searchQuery%";
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = (int)$searchQuery;
    $types .= 'ssi';
}
$query .= " ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

include '../includes/admin_header.php';
?>

<h1 class="admin-page-title"><i class="fas fa-shopping-bag"></i> Manage Orders</h1>

<!-- Filters -->
<div class="admin-filters">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <input type="text" name="search" placeholder="Search by name, email, or order #" value="<?php echo htmlspecialchars($searchQuery); ?>">
        </div>
        <div class="filter-group">
            <select name="status">
                <option value="">All Status</option>
                <?php foreach (['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="manage_orders.php" class="btn btn-outline btn-sm">Clear</a>
    </form>
</div>

<!-- Orders Table -->
<div class="admin-table-card">
    <div class="table-header">
        <h3>Orders (<?php echo $orders->num_rows; ?>)</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                    <td>
                        <?php echo htmlspecialchars($order['full_name']); ?>
                        <br><small><?php echo htmlspecialchars($order['email']); ?></small>
                    </td>
                    <td>
                        <?php
                        $itemStmt = $conn->prepare("SELECT oi.quantity, mi.name FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?");
                        $itemStmt->bind_param("i", $order['id']);
                        $itemStmt->execute();
                        $items = $itemStmt->get_result();
                        while ($oi = $items->fetch_assoc()):
                        ?>
                            <small><?php echo $oi['quantity']; ?>x <?php echo htmlspecialchars($oi['name']); ?></small><br>
                        <?php endwhile; $itemStmt->close(); ?>
                    </td>
                    <td><?php echo formatPrice($order['total_amount']); ?></td>
                    <td><?php echo ucfirst($order['order_type']); ?></td>
                    <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                    <td>
                        <form method="POST" class="status-form">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <?php foreach (['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
