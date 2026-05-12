<?php
require_once '../config.php';
$pageTitle = 'Manage Messages';

// Mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $msgId = (int)$_POST['message_id'];
    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $msgId);
    $stmt->execute();
    $stmt->close();
    redirect('manage_contacts.php');
}

// Delete message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    $msgId = (int)$_POST['message_id'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $msgId);
    if ($stmt->execute()) {
        setFlash('success', 'Message deleted.');
    }
    $stmt->close();
    redirect('manage_contacts.php');
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC");

include '../includes/admin_header.php';
?>

<h1 class="admin-page-title"><i class="fas fa-envelope"></i> Contact Messages</h1>

<div class="admin-table-card">
    <div class="table-header">
        <h3>All Messages (<?php echo $messages->num_rows; ?>)</h3>
    </div>

    <?php if ($messages->num_rows === 0): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No messages yet.</p>
        </div>
    <?php else: ?>
    <div class="messages-list">
        <?php while ($msg = $messages->fetch_assoc()): ?>
        <div class="message-card <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
            <div class="message-header">
                <div>
                    <h4><?php echo htmlspecialchars($msg['name']); ?> <?php echo !$msg['is_read'] ? '<span class="new-badge">New</span>' : ''; ?></h4>
                    <small><?php echo htmlspecialchars($msg['email']); ?> | <?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?></small>
                </div>
                <div class="message-actions">
                    <?php if (!$msg['is_read']): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" name="mark_read" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Mark Read</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this message?')">
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" name="delete_message" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            <?php if ($msg['subject']): ?>
                <p class="message-subject"><strong>Subject:</strong> <?php echo htmlspecialchars($msg['subject']); ?></p>
            <?php endif; ?>
            <p class="message-body"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/admin_footer.php'; ?>
