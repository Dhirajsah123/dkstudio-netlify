<?php
// skillora/includes/header.php

// Ensure we have a consistent base path
// This helps in including files correctly regardless of where this header is included.
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Start a secure session on every page that includes this header.
secure_session_start();

// Define a base URL to make links and asset paths consistent.
// This is more robust than using hardcoded paths.
$base_url = '/skillora'; // Change this if your project is in the root or a different folder.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? sanitize_output($page_title) . ' | Skillora' : 'Skillora - Learn. Upgrade. Earn.'; ?></title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <meta name="description" content="Skillora is an online platform to learn, upgrade your skills, and earn.">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <h1><a href="<?php echo $base_url; ?>/index.php">Skillora</a></h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="<?php echo $base_url; ?>/index.php">Home</a></li>
                    <li><a href="<?php echo $base_url; ?>/membership.php">Membership</a></li>
                    <li><a href="<?php echo $base_url; ?>/courses.php">Courses</a></li>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <li><a href="<?php echo $base_url; ?>/admin/index.php">Admin Panel</a></li>
                        <li><a href="<?php echo $base_url; ?>/admin/logout.php">Logout</a></li>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $base_url; ?>/user/dashboard.php">My Dashboard</a></li>
                        <li><a href="<?php echo $base_url; ?>/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_url; ?>/login.php">Login</a></li>
                        <li><a href="<?php echo $base_url; ?>/membership.php" class="btn">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
        <!-- Main content of the page will be here -->
