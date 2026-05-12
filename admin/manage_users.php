<?php
require_once '../config.php';
$pageTitle = 'Manage Users';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = (int)$_POST['user_id'];
    if ($userId !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            setFlash('success', 'User deleted successfully.');
        } else {
            setFlash('error', 'Cannot delete this user.');
        }
        $stmt->close();
    } else {
        setFlash('error', 'Cannot delete your own account.');
    }
    redirect('manage_users.php');
}

// Handle role toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_role'])) {
    $userId = (int)$_POST['user_id'];
    $newRole = sanitize($_POST['new_role']);
    if ($userId !== $_SESSION['user_id'] && in_array($newRole, ['user', 'admin'])) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $newRole, $userId);
        $stmt->execute();
        setFlash('success', 'User role updated.');
        $stmt->close();
    }
    redirect('manage_users.php');
}

$searchQuery = sanitize($_GET['search'] ?? '');
$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
          (SELECT COUNT(*) FROM reservations WHERE user_id = u.id) as reservation_count
          FROM users u WHERE 1=1";
if (!empty($searchQuery)) {
    $query .= " AND (u.full_name LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR u.email LIKE '%" . $conn->real_escape_string($searchQuery) . "%')";
}
$query .= " ORDER BY u.created_at DESC";
$users = $conn->query($query);

include '../includes/admin_header.php';
?>

<h1 class="admin-page-title"><i class="fas fa-users"></i> Manage Users</h1>

<div class="admin-filters">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
        <a href="manage_users.php" class="btn btn-outline btn-sm">Clear</a>
    </form>
</div>

<div class="admin-table-card">
    <div class="table-header">
        <h3>All Users (<?php echo $users->num_rows; ?>)</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Orders</th>
                    <th>Reservations</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                    <td>
                        <span class="role-badge role-<?php echo $user['role']; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo $user['order_count']; ?></td>
                    <td><?php echo $user['reservation_count']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td class="actions">
                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="new_role" value="<?php echo $user['role'] === 'admin' ? 'user' : 'admin'; ?>">
                                <button type="submit" name="toggle_role" class="btn btn-sm btn-warning" title="Toggle Role">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </form>
                            <?php if ($user['role'] !== 'admin'): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?')">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <small class="text-muted">Current</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
