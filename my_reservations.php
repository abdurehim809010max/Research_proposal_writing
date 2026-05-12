<?php
require_once 'config.php';
$pageTitle = 'My Reservations';

if (!isLoggedIn() || isAdmin()) {
    redirect('login.php');
}

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $resId = (int)$_POST['reservation_id'];
    $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $resId, $_SESSION['user_id']);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        setFlash('success', 'Reservation cancelled successfully.');
    } else {
        setFlash('error', 'Unable to cancel reservation.');
    }
    $stmt->close();
    redirect('my_reservations.php');
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC, reservation_time DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$reservations = $stmt->get_result();
$stmt->close();

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-check"></i> My Reservations</h1>
        <p>View and manage your table reservations</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="text-right mb-2">
            <a href="reservations.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Reservation</a>
        </div>

        <?php if ($reservations->num_rows === 0): ?>
            <div class="empty-state">
                <i class="fas fa-calendar"></i>
                <h3>No reservations yet</h3>
                <p>Book a table for your next dining experience!</p>
                <a href="reservations.php" class="btn btn-primary">Make Reservation</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Special Requests</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($res = $reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo date('M d, Y', strtotime($res['reservation_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($res['reservation_time'])); ?></td>
                            <td><?php echo $res['guests']; ?></td>
                            <td><?php echo $res['special_requests'] ? htmlspecialchars($res['special_requests']) : '-'; ?></td>
                            <td><span class="status-badge status-<?php echo $res['status']; ?>"><?php echo ucfirst($res['status']); ?></span></td>
                            <td>
                                <?php if ($res['status'] === 'pending'): ?>
                                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Cancel this reservation?')">
                                        <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                        <button type="submit" name="cancel_reservation" class="btn btn-sm btn-danger">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
