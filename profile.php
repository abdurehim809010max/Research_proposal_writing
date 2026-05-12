<?php
require_once 'config.php';
$pageTitle = 'My Profile';

if (!isLoggedIn() || isAdmin()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];
$errors = [];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $fullName = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');

        if (empty($fullName)) $errors[] = 'Full name is required.';
        if (strlen($fullName) < 3) $errors[] = 'Full name must be at least 3 characters.';

        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("sssi", $fullName, $phone, $address, $userId);
            if ($stmt->execute()) {
                $_SESSION['full_name'] = $fullName;
                setFlash('success', 'Profile updated successfully.');
                redirect('profile.php');
            }
            $stmt->close();
        }
    }

    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword)) $errors[] = 'Current password is required.';
        if (empty($newPassword)) $errors[] = 'New password is required.';
        if (strlen($newPassword) < 6) $errors[] = 'New password must be at least 6 characters.';
        if ($newPassword !== $confirmPassword) $errors[] = 'New passwords do not match.';

        if (empty($errors)) {
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedPassword, $userId);
                if ($stmt->execute()) {
                    setFlash('success', 'Password changed successfully.');
                    redirect('profile.php');
                }
                $stmt->close();
            } else {
                $errors[] = 'Current password is incorrect.';
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-user"></i> My Profile</h1>
        <p>Manage your account information</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="profile-grid">
            <!-- Profile Info -->
            <div class="profile-card">
                <h3><i class="fas fa-user-edit"></i> Profile Information</h3>
                <form method="POST" action="" id="profileForm">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email (cannot be changed)</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="profile-card">
                <h3><i class="fas fa-lock"></i> Change Password</h3>
                <form method="POST" action="" id="passwordForm">
                    <input type="hidden" name="action" value="change_password">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>

            <!-- Account Stats -->
            <div class="profile-card">
                <h3><i class="fas fa-chart-bar"></i> Account Statistics</h3>
                <?php
                $orderCount = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE user_id = $userId")->fetch_assoc()['cnt'];
                $resCount = $conn->query("SELECT COUNT(*) as cnt FROM reservations WHERE user_id = $userId")->fetch_assoc()['cnt'];
                $totalSpent = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = $userId AND status != 'cancelled'")->fetch_assoc()['total'];
                ?>
                <div class="stats-mini">
                    <div class="stat-mini-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="stat-mini-number"><?php echo $orderCount; ?></span>
                        <span>Total Orders</span>
                    </div>
                    <div class="stat-mini-item">
                        <i class="fas fa-calendar-check"></i>
                        <span class="stat-mini-number"><?php echo $resCount; ?></span>
                        <span>Reservations</span>
                    </div>
                    <div class="stat-mini-item">
                        <i class="fas fa-money-bill"></i>
                        <span class="stat-mini-number"><?php echo formatPrice($totalSpent); ?></span>
                        <span>Total Spent</span>
                    </div>
                </div>
                <p class="text-muted mt-1">Member since: <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
