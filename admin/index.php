<?php
require_once '../config.php';
$pageTitle = 'Dashboard';

// Dashboard statistics
$totalUsers = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'user'")->fetch_assoc()['cnt'];
$totalOrders = $conn->query("SELECT COUNT(*) as cnt FROM orders")->fetch_assoc()['cnt'];
$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status != 'cancelled'")->fetch_assoc()['total'];
$pendingOrders = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE status = 'pending'")->fetch_assoc()['cnt'];
$totalReservations = $conn->query("SELECT COUNT(*) as cnt FROM reservations")->fetch_assoc()['cnt'];
$pendingReservations = $conn->query("SELECT COUNT(*) as cnt FROM reservations WHERE status = 'pending'")->fetch_assoc()['cnt'];
$totalMenuItems = $conn->query("SELECT COUNT(*) as cnt FROM menu_items")->fetch_assoc()['cnt'];
$unreadMessages = $conn->query("SELECT COUNT(*) as cnt FROM contact_messages WHERE is_read = 0")->fetch_assoc()['cnt'];

// Recent orders
$recentOrders = $conn->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");

// Recent reservations
$recentReservations = $conn->query("SELECT r.*, u.full_name FROM reservations r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT 5");

include '../includes/admin_header.php';
?>

<h1 class="admin-page-title"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>

<!-- Stats Cards -->
<div class="dashboard-stats">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-card-info">
            <h3><?php echo $totalOrders; ?></h3>
            <p>Total Orders</p>
        </div>
        <span class="stat-card-badge"><?php echo $pendingOrders; ?> pending</span>
    </div>
    <div class="stat-card stat-card-success">
        <div class="stat-card-icon"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-card-info">
            <h3><?php echo formatPrice($totalRevenue); ?></h3>
            <p>Total Revenue</p>
        </div>
    </div>
    <div class="stat-card stat-card-warning">
        <div class="stat-card-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-card-info">
            <h3><?php echo $totalReservations; ?></h3>
            <p>Reservations</p>
        </div>
        <span class="stat-card-badge"><?php echo $pendingReservations; ?> pending</span>
    </div>
    <div class="stat-card stat-card-info">
        <div class="stat-card-icon"><i class="fas fa-users"></i></div>
        <div class="stat-card-info">
            <h3><?php echo $totalUsers; ?></h3>
            <p>Registered Users</p>
        </div>
    </div>
    <div class="stat-card stat-card-secondary">
        <div class="stat-card-icon"><i class="fas fa-utensils"></i></div>
        <div class="stat-card-info">
            <h3><?php echo $totalMenuItems; ?></h3>
            <p>Menu Items</p>
        </div>
    </div>
    <div class="stat-card stat-card-danger">
        <div class="stat-card-icon"><i class="fas fa-envelope"></i></div>
        <div class="stat-card-info">
            <h3><?php echo $unreadMessages; ?></h3>
            <p>Unread Messages</p>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="dashboard-grid">
    <!-- Recent Orders -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3><i class="fas fa-shopping-bag"></i> Recent Orders</h3>
            <a href="manage_orders.php" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $recentOrders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td><?php echo formatPrice($order['total_amount']); ?></td>
                        <td><?php echo ucfirst($order['order_type']); ?></td>
                        <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                        <td><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3><i class="fas fa-calendar-check"></i> Recent Reservations</h3>
            <a href="manage_reservations.php" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Guest</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Guests</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($res = $recentReservations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $res['id']; ?></td>
                        <td><?php echo htmlspecialchars($res['full_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($res['reservation_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($res['reservation_time'])); ?></td>
                        <td><?php echo $res['guests']; ?></td>
                        <td><span class="status-badge status-<?php echo $res['status']; ?>"><?php echo ucfirst($res['status']); ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
