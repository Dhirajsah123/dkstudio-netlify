<?php
require_once __DIR__ . '/../../php/models/Withdrawal.php';
require_once __DIR__ . '/../../php/models/User.php';

class WithdrawalController {
    private $withdrawalModel;
    private $userModel;

    public function __construct() {
        session_start();
        if (!isset($_SESSION['admin_id'])) {
            header('Location: index.php');
            exit();
        }
        $this->withdrawalModel = new Withdrawal();
        $this->userModel = new User();
    }

    public function index() {
        $withdrawals = $this->withdrawalModel->getAllWithdrawals();
        $data = [
            'withdrawals' => $withdrawals
        ];
        require_once __DIR__ . '/../views/withdrawals.php';
    }

    public function approve($id) {
        if ($this->withdrawalModel->updateStatus($id, 'approved')) {
            header('Location: ../withdrawals.php');
        } else {
            die('Something went wrong');
        }
    }

    public function reject($id) {
        // On rejection, refund the user's balance
        $withdrawal = $this->withdrawalModel->getWithdrawalById($id); // This method needs to be created
        if ($this->withdrawalModel->updateStatus($id, 'rejected')) {
            $this->userModel->updateBalance($withdrawal->user_id, $withdrawal->amount);
            header('Location: ../withdrawals.php');
        } else {
            die('Something went wrong');
        }
    }
}

// Basic routing
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$controller = new WithdrawalController();
if (method_exists($controller, $action)) {
    if ($id) {
        $controller->$action($id);
    } else {
        $controller->$action();
    }
}
?>
