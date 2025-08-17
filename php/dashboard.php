<?php
require_once __DIR__ . '/controllers/DashboardController.php';

$dashboardController = new DashboardController();
$dashboardController->index();
?>
