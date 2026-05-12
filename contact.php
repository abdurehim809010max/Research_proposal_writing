<?php
require_once 'config.php';
$pageTitle = 'Contact Us';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (empty($message)) $errors[] = 'Message is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            setFlash('success', 'Message sent successfully! We will get back to you soon.');
            redirect('contact.php');
        } else {
            $errors[] = 'Failed to send message. Please try again.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Contact Us</h1>
        <p>We'd love to hear from you</p>
    </div>
</section>

<section class="section contact-section">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <div class="contact-info-items">
                    <div class="contact-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Address</h4>
                            <p>Bole Road, Near Edna Mall<br>Addis Ababa, Ethiopia</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>+251 911 000 000</p>
                            <p>+251 111 234 567</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p>info@habesha-kitchen.com</p>
                            <p>reservations@habesha-kitchen.com</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h4>Working Hours</h4>
                            <p>Mon-Fri: 7:00 AM - 10:00 PM</p>
                            <p>Sat-Sun: 8:00 AM - 11:00 PM</p>
                        </div>
                    </div>
                </div>
                <div class="contact-map">
                    <div class="map-placeholder">
                        <i class="fas fa-map"></i>
                        <p>Bole Road, Addis Ababa, Ethiopia</p>
                    </div>
                </div>
            </div>

            <div class="contact-form-container">
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="contact-form" id="contactForm">
                    <h3>Send us a Message</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Your Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" required>
                            <span class="error-text" id="nameError"></span>
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Your Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            <span class="error-text" id="emailError"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject"><i class="fas fa-tag"></i> Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="What is this about?">
                    </div>
                    <div class="form-group">
                        <label for="message"><i class="fas fa-comment"></i> Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                        <span class="error-text" id="messageError"></span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
