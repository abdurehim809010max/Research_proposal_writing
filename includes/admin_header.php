<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    redirect(SITE_URL . '/login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | Admin - ' . SITE_NAME : 'Admin - ' . SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="admin-body">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="<?php echo SITE_URL; ?>/admin/index.php" class="admin-logo">
                <i class="fas fa-utensils"></i>
                <span><?php echo SITE_NAME; ?></span>
            </a>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/admin/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/manage_menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_menu.php' ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> <span>Menu Items</span></a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/manage_categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_categories.php' ? 'active' : ''; ?>"><i class="fas fa-list"></i> <span>Categories</span></a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/manage_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_orders.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/manage_reservations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_reservations.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> <span>Reservations</span></a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/manage_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> <span>Users</span></a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/manage_contacts.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_contacts.php' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?php echo SITE_URL; ?>/index.php"><i class="fas fa-globe"></i> <span>View Site</span></a>
            <a href="<?php echo SITE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </div>
    </aside>

    <!-- Admin Main Content -->
    <div class="admin-main">
        <header class="admin-header">
            <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="admin-header-right">
                <span class="admin-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <div class="admin-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage">
            <p><?php echo $flash['message']; ?></p>
            <button class="flash-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
        <?php endif; ?>

        <div class="admin-content">
