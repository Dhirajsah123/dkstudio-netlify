<?php
// skillora/admin/includes/admin_header.php

// We are in /admin/includes, so we need to go up two levels to find the main includes directory.
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

secure_session_start();

// This is the gatekeeper for the entire admin panel.
// If 'admin_id' is not set in the session, the user is not a logged-in admin.
if (!isset($_SESSION['admin_id'])) {
    // We need to construct the correct path to login.php from the web root.
    $base_url = '/skillora'; // This should ideally be in a central config file.
    redirect($base_url . '/admin/login.php');
}

$base_url = '/skillora';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? sanitize_output($page_title) . ' | Skillora Admin' : 'Skillora Admin Panel'; ?></title>
    <!-- Using the main stylesheet as a base -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <!-- A new, specific stylesheet for admin panel overrides and layout -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/admin_style.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h3>Skillora Admin</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/index.php">Dashboard</a>
                    </li>
                    <!-- Add more links as pages are created -->
                    <li><a href="#">Users</a></li>
                    <li><a href="#">Courses</a></li>
                    <li><a href="#">Memberships</a></li>
                    <li><a href="#">All Payments</a></li>
                    <li class="logout-link"><a href="<?php echo $base_url; ?>/admin/logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="admin-main-content">
            <header class="admin-main-header">
                <div class="welcome-message">
                    Welcome back, <strong><?php echo sanitize_output($_SESSION['admin_name']); ?></strong>!
                </div>
            </header>
            <div class="admin-content-area">
                <!-- Page-specific content starts here -->
                <?php if (isset($page_title)): ?>
                    <h1><?php echo sanitize_output($page_title); ?></h1>
                <?php endif; ?>
                <hr>
