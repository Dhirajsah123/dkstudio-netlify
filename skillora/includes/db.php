<?php
// skillora/includes/db.php

/**
 * Database Configuration
 *
 * Defines constants for database credentials and establishes a connection.
 * Using constants makes it easy to change the configuration in one place.
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default password for XAMPP/WAMP is empty.
define('DB_NAME', 'skillora');

/**
 * Establish Database Connection
 *
 * Creates a new mysqli object to connect to the database.
 * The script will terminate with an error message if the connection fails.
 */
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($mysqli->connect_error) {
    // Using die() is a simple way to halt execution on a critical error like DB connection failure.
    die('Database Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

/**
 * Set Character Set
 *
 * It's a good practice to set the character set to utf8mb4 to support a wide range of characters
 * and prevent potential encoding issues with user-submitted data.
 */
$mysqli->set_charset("utf8mb4");

?>
