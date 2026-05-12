<?php
require_once 'config.php';
$pageTitle = 'Reservations';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        setFlash('error', 'Please login to make a reservation.');
        redirect('login.php');
    }

    $date = sanitize($_POST['reservation_date'] ?? '');
    $time = sanitize($_POST['reservation_time'] ?? '');
    $guests = (int)($_POST['guests'] ?? 1);
    $requests = sanitize($_POST['special_requests'] ?? '');

    if (empty($date)) $errors[] = 'Reservation date is required.';
    if (empty($time)) $errors[] = 'Reservation time is required.';
    if ($guests < 1 || $guests > 20) $errors[] = 'Number of guests must be between 1 and 20.';
    if (strtotime($date) < strtotime('today')) $errors[] = 'Reservation date must be in the future.';

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO reservations (user_id, reservation_date, reservation_time, guests, special_requests) VALUES (?, ?, ?, ?, ?)");
        $userId = $_SESSION['user_id'];
        $stmt->bind_param("issis", $userId, $date, $time, $guests, $requests);

        if ($stmt->execute()) {
            setFlash('success', 'Reservation submitted successfully! We will confirm shortly.');
            redirect('my_reservations.php');
        } else {
            $errors[] = 'Failed to submit reservation. Please try again.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-check"></i> Make a Reservation</h1>
        <p>Book your table at Habesha Kitchen</p>
    </div>
</section>

<section class="section reservation-section">
    <div class="container">
        <div class="reservation-layout">
            <div class="reservation-info">
                <h3>Dining Information</h3>
                <div class="info-card">
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h4>Opening Hours</h4>
                            <p>Mon-Fri: 7:00 AM - 10:00 PM</p>
                            <p>Saturday: 8:00 AM - 11:00 PM</p>
                            <p>Sunday: 9:00 AM - 9:00 PM</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <div>
                            <h4>Capacity</h4>
                            <p>Up to 20 guests per reservation</p>
                            <p>For larger events, contact us directly</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Contact</h4>
                            <p>+251 911 000 000</p>
                            <p>info@habesha-kitchen.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reservation-form-container">
                <?php if (!isLoggedIn()): ?>
                    <div class="auth-prompt">
                        <i class="fas fa-lock"></i>
                        <h3>Login Required</h3>
                        <p>Please login or create an account to make a reservation.</p>
                        <a href="login.php" class="btn btn-primary">Login</a>
                        <a href="signup.php" class="btn btn-outline">Sign Up</a>
                    </div>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="reservation-form" id="reservationForm">
                        <h3>Reserve Your Table</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="reservation_date"><i class="fas fa-calendar"></i> Date</label>
                                <input type="date" id="reservation_date" name="reservation_date" min="<?php echo date('Y-m-d'); ?>" required>
                                <span class="error-text" id="dateError"></span>
                            </div>
                            <div class="form-group">
                                <label for="reservation_time"><i class="fas fa-clock"></i> Time</label>
                                <select id="reservation_time" name="reservation_time" required>
                                    <option value="">Select Time</option>
                                    <?php for ($h = 7; $h <= 21; $h++): ?>
                                        <option value="<?php echo sprintf('%02d:00:00', $h); ?>"><?php echo sprintf('%02d:00', $h); ?></option>
                                        <option value="<?php echo sprintf('%02d:30:00', $h); ?>"><?php echo sprintf('%02d:30', $h); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <span class="error-text" id="timeError"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="guests"><i class="fas fa-users"></i> Number of Guests</label>
                            <input type="number" id="guests" name="guests" min="1" max="20" value="2" required>
                            <span class="error-text" id="guestsError"></span>
                        </div>
                        <div class="form-group">
                            <label for="special_requests"><i class="fas fa-comment"></i> Special Requests (Optional)</label>
                            <textarea id="special_requests" name="special_requests" rows="3" placeholder="Allergies, dietary requirements, birthday celebration, etc."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-check"></i> Submit Reservation
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
