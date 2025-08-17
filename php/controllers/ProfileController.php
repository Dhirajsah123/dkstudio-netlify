<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/CSRF.php';

class ProfileController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        }

        $user = $this->userModel->findUserById($_SESSION['user_id']);

        $data = [
            'user' => $user
        ];

        require_once __DIR__ . '/../views/profile.php';
    }

    public function updateProfile() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            if (!CSRF::validateToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
                exit;
            }

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $_SESSION['user_id'],
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email'])
            ];

            if ($this->userModel->updateProfile($data)) {
                $_SESSION['user_name'] = $data['username'];
                $_SESSION['user_email'] = $data['email'];
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
            }
        }
    }

    public function updatePassword() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            if (!CSRF::validateToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
                exit;
            }

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $_SESSION['user_id'],
                'current_password' => $_POST['current_password'],
                'new_password' => $_POST['new_password'],
                'confirm_new_password' => $_POST['confirm_new_password']
            ];

            $user = $this->userModel->findUserById($_SESSION['user_id']);

            if (!password_verify($data['current_password'], $user->password)) {
                echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
                exit;
            }

            if (strlen($data['new_password']) < 6) {
                echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
                exit;
            }

            if ($data['new_password'] !== $data['confirm_new_password']) {
                echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
                exit;
            }

            $data['new_password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);

            if ($this->userModel->updatePassword($data['id'], $data['new_password'])) {
                echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
            }
        }
    }
}

// Basic routing
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $controller = new ProfileController();
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}
?>
