<?php
require_once __DIR__ . '/../models/Admin.php';

class AdminController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new Admin();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => ''
            ];

            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            if (empty($data['username_err']) && empty($data['password_err'])) {
                $loggedInAdmin = $this->adminModel->login($data['username'], $data['password']);
                if ($loggedInAdmin) {
                    $this->createAdminSession($loggedInAdmin);
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
                }
            } else {
                echo json_encode(['success' => false, 'errors' => $data]);
            }
        }
    }

    public function createAdminSession($admin) {
        session_start();
        $_SESSION['admin_id'] = $admin->id;
        $_SESSION['admin_username'] = $admin->username;
    }

    public function logout() {
        session_start();
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        session_destroy();
        header('Location: index.php');
        exit();
    }
}

// Basic routing
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $controller = new AdminController();
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}
?>
