<?php
require_once __DIR__ . '/../../php/models/Ad.php';

class AdController {
    private $adModel;

    public function __construct() {
        session_start();
        if (!isset($_SESSION['admin_id'])) {
            header('Location: index.php');
            exit();
        }
        $this->adModel = new Ad();
    }

    public function index() {
        $ads = $this->adModel->getActiveAds();
        $data = [
            'ads' => $ads
        ];
        require_once __DIR__ . '/../views/ads.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'points' => trim($_POST['points']),
                'url' => trim($_POST['url']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            if ($this->adModel->addAd($data)) {
                header('Location: ../ads.php');
            } else {
                die('Something went wrong');
            }
        }
        require_once __DIR__ . '/../views/add_ad.php';
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'points' => trim($_POST['points']),
                'url' => trim($_POST['url']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            if ($this->adModel->updateAd($data)) {
                header('Location: ../ads.php');
            } else {
                die('Something went wrong');
            }
        }

        $ad = $this->adModel->getAdById($id);
        $data = ['ad' => $ad];
        require_once __DIR__ . '/../views/edit_ad.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->adModel->deleteAd($id)) {
                header('Location: ../ads.php');
            } else {
                die('Something went wrong');
            }
        }
    }
}

// Basic routing
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$controller = new AdController();
if (method_exists($controller, $action)) {
    if ($id) {
        $controller->$action($id);
    } else {
        $controller->$action();
    }
}
?>
