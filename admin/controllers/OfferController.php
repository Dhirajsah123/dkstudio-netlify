<?php
require_once __DIR__ . '/../../php/models/Offer.php';

class OfferController {
    private $offerModel;

    public function __construct() {
        session_start();
        if (!isset($_SESSION['admin_id'])) {
            header('Location: index.php');
            exit();
        }
        $this->offerModel = new Offer();
    }

    public function index() {
        $offers = $this->offerModel->getActiveOffers();
        $data = [
            'offers' => $offers
        ];
        require_once __DIR__ . '/../views/offers.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'points' => trim($_POST['points']),
                'url' => trim($_POST['url']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            if ($this->offerModel->addOffer($data)) {
                header('Location: ../offers.php');
            } else {
                die('Something went wrong');
            }
        }
        require_once __DIR__ . '/../views/add_offer.php';
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'points' => trim($_POST['points']),
                'url' => trim($_POST['url']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            if ($this->offerModel->updateOffer($data)) {
                header('Location: ../offers.php');
            } else {
                die('Something went wrong');
            }
        }

        $offer = $this->offerModel->getOfferById($id);
        $data = ['offer' => $offer];
        require_once __DIR__ . '/../views/edit_offer.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->offerModel->deleteOffer($id)) {
                header('Location: ../offers.php');
            } else {
                die('Something went wrong');
            }
        }
    }
}

// Basic routing
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$controller = new OfferController();
if (method_exists($controller, $action)) {
    if ($id) {
        $controller->$action($id);
    } else {
        $controller->$action();
    }
}
?>
