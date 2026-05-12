<?php
require_once 'config.php';
$pageTitle = 'Sign Up';

if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];
$formData = ['full_name' => '', 'email' => '', 'phone' => '', 'address' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['full_name'] = sanitize($_POST['full_name'] ?? '');
    $formData['email'] = sanitize($_POST['email'] ?? '');
    $formData['phone'] = sanitize($_POST['phone'] ?? '');
    $formData['address'] = sanitize($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($formData['full_name'])) $errors[] = 'Full name is required.';
    if (strlen($formData['full_name']) < 3) $errors[] = 'Full name must be at least 3 characters.';
    if (empty($formData['email'])) $errors[] = 'Email is required.';
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (empty($password)) $errors[] = 'Password is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $formData['email']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'Email already registered. Please login instead.';
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $formData['full_name'], $formData['email'], $formData['phone'], $hashedPassword, $formData['address']);

        if ($stmt->execute()) {
            setFlash('success', 'Registration successful! Please login.');
            redirect('login.php');
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-card auth-card-wide">
            <div class="auth-header">
                <h2><i class="fas fa-user-plus"></i> Create Account</h2>
                <p>Join Habesha Kitchen and enjoy our delicious Ethiopian cuisine.</p>
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

            <form method="POST" action="" class="auth-form" id="signupForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($formData['full_name']); ?>" placeholder="Enter your full name" required>
                        <span class="error-text" id="nameError"></span>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" placeholder="Enter your email" required>
                        <span class="error-text" id="emailError"></span>
                        <span class="success-text" id="emailSuccess"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($formData['phone']); ?>" placeholder="+251 9XX XXX XXX">
                        <span class="error-text" id="phoneError"></span>
                    </div>
                    <div class="form-group">
                        <label for="address"><i class="fas fa-map-marker-alt"></i> Address</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($formData['address']); ?>" placeholder="City, Area">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Min. 6 characters" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="error-text" id="passwordError"></span>
                        <div class="password-strength" id="passwordStrength"></div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="error-text" id="confirmError"></span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login Here</a></p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
