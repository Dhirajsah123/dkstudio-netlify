<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}
require_once __DIR__ . '/views/login.php';
?>
