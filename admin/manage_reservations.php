<?php
require_once '../config.php';
$pageTitle = 'Manage Reservations';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $resId = (int)$_POST['reservation_id'];
    $status = sanitize($_POST['status']);
    $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $resId);
    if ($stmt->execute()) {
        setFlash('success', 'Reservation status updated.');
    }
    $stmt->close();
    redirect('manage_reservations.php');
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reservation'])) {
    $resId = (int)$_POST['reservation_id'];
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $resId);
    if ($stmt->execute()) {
        setFlash('success', 'Reservation deleted.');
    }
    $stmt->close();
    redirect('manage_reservations.php');
}

$statusFilter = sanitize($_GET['status'] ?? '');
$query = "SELECT r.*, u.full_name, u.email, u.phone FROM reservations r JOIN users u ON r.user_id = u.id WHERE 1=1";
if (!empty($statusFilter)) {
    $query .= " AND r.status = '" . $conn->real_escape_string($statusFilter) . "'";
}
$query .= " ORDER BY r.reservation_date DESC, r.reservation_time DESC";
$reservations = $conn->query($query);

include '../includes/admin_header.php';
?>

<h1 class="admin-page-title"><i class="fas fa-calendar-check"></i> Manage Reservations</h1>

<div class="admin-filters">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <select name="status">
                <option value="">All Status</option>
                <?php foreach (['pending', 'confirmed', 'cancelled', 'completed'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="manage_reservations.php" class="btn btn-outline btn-sm">Clear</a>
    </form>
</div>

<div class="admin-table-card">
    <div class="table-header">
        <h3>All Reservations (<?php echo $reservations->num_rows; ?>)</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Guest</th>
                    <th>Contact</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Requests</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($res = $reservations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $res['id']; ?></td>
                    <td><?php echo htmlspecialchars($res['full_name']); ?></td>
                    <td>
                        <small><?php echo htmlspecialchars($res['email']); ?></small><br>
                        <small><?php echo htmlspecialchars($res['phone'] ?? ''); ?></small>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($res['reservation_date'])); ?></td>
                    <td><?php echo date('h:i A', strtotime($res['reservation_time'])); ?></td>
                    <td><?php echo $res['guests']; ?></td>
                    <td><?php echo $res['special_requests'] ? htmlspecialchars(substr($res['special_requests'], 0, 40)) : '-'; ?></td>
                    <td><span class="status-badge status-<?php echo $res['status']; ?>"><?php echo ucfirst($res['status']); ?></span></td>
                    <td class="actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <?php foreach (['pending', 'confirmed', 'cancelled', 'completed'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $res['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this reservation?')">
                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                            <button type="submit" name="delete_reservation" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
