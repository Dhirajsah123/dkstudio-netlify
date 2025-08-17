<?php
require_once __DIR__ . '/../models/Offer.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Transaction.php';

class OfferwallController {
    private $offerModel;
    private $userModel;
    private $transactionModel;

    public function __construct() {
        $this->offerModel = new Offer();
        $this->userModel = new User();
        $this->transactionModel = new Transaction();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        }

        $offers = $this->offerModel->getActiveOffers();

        $data = [
            'offers' => $offers
        ];

        require_once __DIR__ . '/../views/offerwall.php';
    }

    public function completeOffer() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $offer_id = $_POST['offer_id'];
            $user_id = $_SESSION['user_id'];

            $offer = $this->offerModel->getOfferById($offer_id);

            if ($offer) {
                // Credit user's account
                $this->userModel->updateBalance($user_id, $offer->points);

                // Create a transaction record
                $this->transactionModel->create([
                    'user_id' => $user_id,
                    'type' => 'credit',
                    'description' => 'Completed offer: ' . $offer->name,
                    'amount' => $offer->points
                ]);

                echo json_encode(['success' => true, 'message' => 'Offer completed successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid offer.']);
            }
        }
    }
}

// Basic routing
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $controller = new OfferwallController();
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}
?>
