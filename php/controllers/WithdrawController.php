<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Withdrawal.php';
require_once __DIR__ . '/../models/Transaction.php';

class WithdrawController {
    private $userModel;
    private $withdrawalModel;
    private $transactionModel;

    public function __construct() {
        $this->userModel = new User();
        $this->withdrawalModel = new Withdrawal();
        $this->transactionModel = new Transaction();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        }

        $user = $this->userModel->findUserById($_SESSION['user_id']);
        $withdrawal_history = $this->withdrawalModel->getWithdrawalHistory($_SESSION['user_id']);

        $data = [
            'user' => $user,
            'withdrawal_history' => $withdrawal_history
        ];

        require_once __DIR__ . '/../views/withdraw.php';
    }

    public function requestWithdrawal() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $user_id = $_SESSION['user_id'];
            $user = $this->userModel->findUserById($user_id);

            $data = [
                'user_id' => $user_id,
                'method' => $_POST['method'],
                'amount' => $_POST['amount'],
                'details' => $_POST['details']
            ];

            if ($data['amount'] > $user->balance) {
                echo json_encode(['success' => false, 'message' => 'Insufficient balance.']);
                exit;
            }

            if ($data['amount'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid amount.']);
                exit;
            }

            // Create withdrawal request
            if ($this->withdrawalModel->create($data)) {
                // Deduct balance
                $this->userModel->updateBalance($user_id, -$data['amount']);

                // Create a transaction record
                $this->transactionModel->create([
                    'user_id' => $user_id,
                    'type' => 'debit',
                    'description' => 'Withdrawal request: ' . $data['method'],
                    'amount' => $data['amount']
                ]);

                echo json_encode(['success' => true, 'message' => 'Withdrawal request submitted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to submit withdrawal request.']);
            }
        }
    }
}

// Basic routing
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $controller = new WithdrawController();
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}
?>
