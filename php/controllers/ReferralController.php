<?php
require_once __DIR__ . '/../models/User.php';

class ReferralController {
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

        $user_id = $_SESSION['user_id'];
        $user = $this->userModel->findUserById($user_id);
        $referredUsers = $this->userModel->getReferredUsers($user_id);
        $referralSummary = $this->userModel->getReferralSummary($user_id);

        $data = [
            'user' => $user,
            'referred_users' => $referredUsers,
            'referral_summary' => $referralSummary
        ];

        require_once __DIR__ . '/../views/referral.php';
    }
}
?>
