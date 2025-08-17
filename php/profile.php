<?php
require_once __DIR__ . '/controllers/ProfileController.php';

$profileController = new ProfileController();
$profileController->index();
?>
