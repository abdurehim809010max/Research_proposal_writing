    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3><i class="fas fa-utensils"></i> <?php echo SITE_NAME; ?></h3>
                    <p><?php echo SITE_TAGLINE; ?> - Experience the rich flavors of Ethiopia in every bite. From traditional Doro Wot to freshly brewed Buna, we bring the taste of home to your table.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-telegram"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/menu.php">Our Menu</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/reservations.php">Reservations</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Opening Hours</h4>
                    <ul class="hours-list">
                        <li><span>Monday - Friday:</span> 7:00 AM - 10:00 PM</li>
                        <li><span>Saturday:</span> 8:00 AM - 11:00 PM</li>
                        <li><span>Sunday:</span> 9:00 AM - 9:00 PM</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact Info</h4>
                    <ul class="contact-list">
                        <li><i class="fas fa-map-marker-alt"></i> Bole Road, Addis Ababa, Ethiopia</li>
                        <li><i class="fas fa-phone"></i> +251 911 000 000</li>
                        <li><i class="fas fa-envelope"></i> info@habesha-kitchen.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved. | Designed for Web Programming Technologies Course</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo SITE_URL; ?>/js/main.js"></script>
    <?php if (isset($extraJS)) echo $extraJS; ?>
</body>
</html>
