<?php
require_once __DIR__ . '/../controllers/ApiController.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // A proper API would use headers for authentication (e.g., JWT)
    // For simplicity, we'll use a session-based approach for now,
    // assuming the app would manage the session cookie.
    // A user ID could also be passed as a GET parameter for statelessness.
    session_start();
    if (isset($_SESSION['user_id'])) {
        $apiController = new ApiController();
        $apiController->getDashboardData($_SESSION['user_id']);
    } else if (isset($_GET['user_id'])) { // Fallback for stateless
        $apiController = new ApiController();
        $apiController->getDashboardData($_GET['user_id']);
    }
    else {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
}
?>
