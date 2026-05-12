/* ============================================
   Habesha Kitchen - Main JavaScript
   Client-side validation, DOM manipulation, 
   and interactivity
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Navbar Scroll Effect ----
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ---- Mobile Navigation Toggle ----
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });

        // Mobile dropdown toggle
        const dropdowns = navMenu.querySelectorAll('.dropdown');
        dropdowns.forEach(function (dropdown) {
            dropdown.addEventListener('click', function (e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    this.classList.toggle('active');
                }
            });
        });
    }

    // ---- Flash Message Auto-dismiss ----
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(function () {
            flashMessage.style.transition = 'opacity 0.5s ease';
            flashMessage.style.opacity = '0';
            setTimeout(function () { flashMessage.remove(); }, 500);
        }, 5000);
    }

    // ---- Counter Animation ----
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0) {
        const observerOptions = { threshold: 0.5 };
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        statNumbers.forEach(function (el) { observer.observe(el); });
    }

    function animateCounter(element) {
        var target = parseInt(element.getAttribute('data-count'));
        var current = 0;
        var increment = Math.ceil(target / 60);
        var timer = setInterval(function () {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = current.toLocaleString();
            if (target <= 5) {
                element.textContent = current + '.0';
            }
        }, 30);
    }

    // ---- Form Validation ----

    // Login Form
    var loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            var isValid = true;
            var email = document.getElementById('email');
            var password = document.getElementById('password');

            clearErrors();

            if (!email.value.trim()) {
                showError('emailError', 'Email is required.');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError('emailError', 'Please enter a valid email.');
                isValid = false;
            }

            if (!password.value) {
                showError('passwordError', 'Password is required.');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // Signup Form
    var signupForm = document.getElementById('signupForm');
    if (signupForm) {
        var emailInput = document.getElementById('email');

        // Real-time email availability check (AJAX/fetch - asynchronous)
        if (emailInput) {
            var emailTimer;
            emailInput.addEventListener('input', function () {
                clearTimeout(emailTimer);
                var email = this.value.trim();
                document.getElementById('emailError').textContent = '';
                document.getElementById('emailSuccess').textContent = '';

                if (email && isValidEmail(email)) {
                    emailTimer = setTimeout(function () {
                        checkEmailAvailability(email);
                    }, 500);
                }
            });
        }

        // Password strength indicator
        var passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function () {
                showPasswordStrength(this.value);
            });
        }

        signupForm.addEventListener('submit', function (e) {
            var isValid = true;
            clearErrors();

            var fullName = document.getElementById('full_name');
            var email = document.getElementById('email');
            var password = document.getElementById('password');
            var confirmPassword = document.getElementById('confirm_password');

            if (!fullName.value.trim()) {
                showError('nameError', 'Full name is required.');
                isValid = false;
            } else if (fullName.value.trim().length < 3) {
                showError('nameError', 'Name must be at least 3 characters.');
                isValid = false;
            }

            if (!email.value.trim()) {
                showError('emailError', 'Email is required.');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError('emailError', 'Please enter a valid email.');
                isValid = false;
            }

            if (!password.value) {
                showError('passwordError', 'Password is required.');
                isValid = false;
            } else if (password.value.length < 6) {
                showError('passwordError', 'Password must be at least 6 characters.');
                isValid = false;
            }

            if (password.value !== confirmPassword.value) {
                showError('confirmError', 'Passwords do not match.');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // Contact Form
    var contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            var isValid = true;
            clearErrors();

            var name = document.getElementById('name');
            var email = document.getElementById('email');
            var message = document.getElementById('message');

            if (!name.value.trim()) {
                showError('nameError', 'Name is required.');
                isValid = false;
            }

            if (!email.value.trim()) {
                showError('emailError', 'Email is required.');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError('emailError', 'Please enter a valid email.');
                isValid = false;
            }

            if (!message.value.trim()) {
                showError('messageError', 'Message is required.');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // Reservation Form
    var reservationForm = document.getElementById('reservationForm');
    if (reservationForm) {
        reservationForm.addEventListener('submit', function (e) {
            var isValid = true;
            clearErrors();

            var date = document.getElementById('reservation_date');
            var time = document.getElementById('reservation_time');
            var guests = document.getElementById('guests');

            if (!date.value) {
                showError('dateError', 'Date is required.');
                isValid = false;
            } else if (new Date(date.value) < new Date().setHours(0, 0, 0, 0)) {
                showError('dateError', 'Date must be in the future.');
                isValid = false;
            }

            if (!time.value) {
                showError('timeError', 'Time is required.');
                isValid = false;
            }

            if (!guests.value || guests.value < 1 || guests.value > 20) {
                showError('guestsError', 'Guests must be between 1 and 20.');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // ---- Delivery Address Toggle ----
    var orderType = document.getElementById('order_type');
    var deliveryGroup = document.getElementById('deliveryAddressGroup');
    if (orderType && deliveryGroup) {
        orderType.addEventListener('change', function () {
            deliveryGroup.style.display = this.value === 'delivery' ? 'block' : 'none';
        });
    }

    // ---- Helper Functions ----

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showError(elementId, message) {
        var el = document.getElementById(elementId);
        if (el) el.textContent = message;
    }

    function clearErrors() {
        var errorTexts = document.querySelectorAll('.error-text, .success-text');
        errorTexts.forEach(function (el) { el.textContent = ''; });
    }

    // Asynchronous email check using Fetch API
    function checkEmailAvailability(email) {
        fetch('api/check_email.php?email=' + encodeURIComponent(email))
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.available) {
                    document.getElementById('emailSuccess').textContent = data.message;
                    document.getElementById('emailError').textContent = '';
                } else {
                    document.getElementById('emailError').textContent = data.message;
                    document.getElementById('emailSuccess').textContent = '';
                }
            })
            .catch(function () {
                // Silently fail for connectivity issues
            });
    }

    function showPasswordStrength(password) {
        var strengthDiv = document.getElementById('passwordStrength');
        if (!strengthDiv) return;

        var strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        var colors = ['#e74c3c', '#e67e22', '#f1c40f', '#2ecc71', '#27ae60'];
        var widths = ['20%', '40%', '60%', '80%', '100%'];

        strengthDiv.innerHTML = '<div class="strength-bar" style="width:' + widths[Math.min(strength, 4)] + ';background:' + colors[Math.min(strength, 4)] + '"></div>';
    }
});

// ---- Password Toggle ----
function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    var icon = field.parentElement.querySelector('.toggle-password i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
