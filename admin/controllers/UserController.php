<?php
require_once __DIR__ . '/../../php/models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        session_start();
        if (!isset($_SESSION['admin_id'])) {
            header('Location: index.php');
            exit();
        }
        $this->userModel = new User();
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        $data = [
            'users' => $users
        ];
        require_once __DIR__ . '/../views/users.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->userModel->deleteUser($id)) {
                header('Location: ../users.php');
            } else {
                die('Something went wrong');
            }
        }
    }

    public function edit($id) {
        $user = $this->userModel->findUserById($id);
        $data = [
            'user' => $user
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $updateData = [
                'id' => $id,
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'balance' => trim($_POST['balance']),
                'email_verified' => isset($_POST['email_verified']) ? 1 : 0
            ];

            if ($this->userModel->updateProfileByAdmin($updateData)) {
                header('Location: ../users.php');
            } else {
                die('Something went wrong');
            }
        }

        require_once __DIR__ . '/../views/edit_user.php';
    }
}

// Basic routing
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$controller = new UserController();
if (method_exists($controller, $action)) {
    if ($id) {
        $controller->$action($id);
    } else {
        $controller->$action();
    }
}
?>
