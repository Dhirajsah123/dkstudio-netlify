<?php
// skillora/admin/logout.php

require_once __DIR__ . '/../includes/functions.php';

secure_session_start();

// Step 1: Unset all of the session variables.
$_SESSION = [];

// Step 2: Destroy the session cookie.
// This is a robust way to ensure the cookie is cleared from the browser.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Step 3: Finally, destroy the session.
session_destroy();

// Step 4: Redirect to the login page.
// The user is now logged out and sent to a non-authenticated page.
$base_url = '/skillora'; // This should ideally be in a central config file.
redirect($base_url . '/admin/login.php?status=loggedout');
?>
