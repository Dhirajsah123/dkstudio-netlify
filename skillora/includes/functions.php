<?php
// skillora/includes/functions.php

/**
 * Starts a secure PHP session.
 *
 * This function configures session settings for better security before starting the session.
 * It helps prevent session fixation and XSS attacks related to session cookies.
 */
function secure_session_start() {
    // Must be called before session_start().
    ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.

    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $cookieParams['lifetime'],
        'path' => $cookieParams['path'],
        'domain' => $_SERVER['HTTP_HOST'], // Or your specific domain
        'secure' => isset($_SERVER['HTTPS']), // Only send cookie over HTTPS
        'httponly' => true, // Prevent JavaScript access to session cookie
        'samesite' => 'Lax' // Mitigates CSRF attacks. 'Strict' is more secure but can affect usability.
    ]);

    // Start the session if one isn't already active.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Sanitizes a string for safe output to HTML to prevent XSS.
 *
 * This is a crucial function to use anytime you are echoing user-provided
 * or database-sourced content to a web page.
 *
 * @param string $data The raw data to be sanitized.
 * @return string The sanitized data.
 */
function sanitize_output($data) {
    // The ENT_QUOTES flag converts both double and single quotes.
    // UTF-8 is specified to ensure proper character encoding.
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirects the user to another page.
 *
 * A simple wrapper for a common task. It's important to call exit()
 * immediately after the header() function to ensure the script stops
 * executing and the redirect happens.
 *
 * @param string $url The URL to redirect to.
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

?>
