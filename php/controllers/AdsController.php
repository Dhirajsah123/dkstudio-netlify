<?php
require_once __DIR__ . '/../models/Ad.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Transaction.php';

class AdsController {
    private $adModel;
    private $userModel;
    private $transactionModel;

    public function __construct() {
        $this->adModel = new Ad();
        $this->userModel = new User();
        $this->transactionModel = new Transaction();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        }

        $ads = $this->adModel->getActiveAds();

        $data = [
            'ads' => $ads
        ];

        require_once __DIR__ . '/../views/ads.php';
    }

    public function completeAd() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $ad_id = $_POST['ad_id'];
            $user_id = $_SESSION['user_id'];

            $ad = $this->adModel->getAdById($ad_id);

            if ($ad) {
                // Credit user's account
                $this->userModel->updateBalance($user_id, $ad->points);

                // Create a transaction record
                $this->transactionModel->create([
                    'user_id' => $user_id,
                    'type' => 'credit',
                    'description' => 'Watched ad: ' . $ad->title,
                    'amount' => $ad->points
                ]);

                echo json_encode(['success' => true, 'message' => 'Ad watched successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid ad.']);
            }
        }
    }
}

// Basic routing
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $controller = new AdsController();
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}
?>
