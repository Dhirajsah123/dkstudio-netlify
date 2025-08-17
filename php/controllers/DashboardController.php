<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/OfferwallProvider.php';
require_once __DIR__ . '/../models/Ad.php';

class DashboardController {
    private $userModel;
    private $transactionModel;
    private $offerwallProviderModel;
    private $adModel;

    public function __construct() {
        $this->userModel = new User();
        $this->transactionModel = new Transaction();
        $this->offerwallProviderModel = new OfferwallProvider();
        $this->adModel = new Ad();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        }

        $user = $this->userModel->findUserById($_SESSION['user_id']);
        $recentTransactions = $this->transactionModel->getRecentTransactions($_SESSION['user_id']);
        $referralSummary = $this->userModel->getReferralSummary($_SESSION['user_id']);
        $offerwallProviders = $this->offerwallProviderModel->getActiveProviders();
        $ads = $this->adModel->getActiveAds();

        $data = [
            'user' => $user,
            'recent_transactions' => $recentTransactions,
            'referral_summary' => $referralSummary,
            'offerwall_providers' => $offerwallProviders,
            'ads' => $ads
        ];

        // Load view
        require_once __DIR__ . '/../views/dashboard.php';
    }
}
?>
