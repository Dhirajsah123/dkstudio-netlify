<?php
require_once __DIR__ . '/../controllers/ApiController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiController = new ApiController();
    $apiController->login();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
}
?>
