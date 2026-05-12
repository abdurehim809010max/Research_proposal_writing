<?php
require_once 'config.php';
$pageTitle = 'Login';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/index.php' : 'index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) $errors[] = 'Email is required.';
    if (empty($password)) $errors[] = 'Password is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                setFlash('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');
                redirect($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
            } else {
                $errors[] = 'Invalid email or password.';
            }
        } else {
            $errors[] = 'Invalid email or password.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
                <p>Welcome back! Please login to your account.</p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="auth-form" id="loginForm">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="Enter your email" required>
                    <span class="error-text" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <span class="error-text" id="passwordError"></span>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.php">Sign Up Here</a></p>
            </div>

            <div class="demo-credentials">
                <h4>Demo Credentials:</h4>
                <p><strong>Admin:</strong> admin@habesha.com / password</p>
                <p><strong>User:</strong> abebe@example.com / password</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
