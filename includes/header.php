<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
                <i class="fas fa-utensils"></i>
                <span><?php echo SITE_NAME; ?></span>
            </a>

            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span class="hamburger"></span>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link">Home</a></li>
                <li><a href="<?php echo SITE_URL; ?>/menu.php" class="nav-link">Menu</a></li>
                <li><a href="<?php echo SITE_URL; ?>/reservations.php" class="nav-link">Reservations</a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact.php" class="nav-link">Contact</a></li>

                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?php echo SITE_URL; ?>/admin/index.php" class="nav-link">Admin Panel</a></li>
                    <?php else: ?>
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle">My Account <i class="fas fa-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo SITE_URL; ?>/profile.php">Profile</a></li>
                                <li><a href="<?php echo SITE_URL; ?>/my_orders.php">My Orders</a></li>
                                <li><a href="<?php echo SITE_URL; ?>/my_reservations.php">My Reservations</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/cart.php" class="nav-link cart-link">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count" id="cartCount"><?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li><a href="<?php echo SITE_URL; ?>/logout.php" class="nav-link btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo SITE_URL; ?>/login.php" class="nav-link">Login</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/signup.php" class="nav-link btn-signup">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php $flash = getFlash(); if ($flash): ?>
    <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage">
        <div class="container">
            <p><?php echo $flash['message']; ?></p>
            <button class="flash-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <main>
