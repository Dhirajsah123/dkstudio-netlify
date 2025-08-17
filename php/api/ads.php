<?php
require_once __DIR__ . '/../controllers/ApiController.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $apiController = new ApiController();
    $apiController->getAds();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
}
?>
