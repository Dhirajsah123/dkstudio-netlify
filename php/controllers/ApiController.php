<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/config.php';

class ApiController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        // Set header to return JSON
        header('Content-Type: application/json');
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON.']);
            exit;
        }

        $userData = [
            'username' => trim($data->username),
            'email' => trim($data->email),
            'password' => trim($data->password),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];

        // Basic validation
        if (empty($userData['username']) || empty($userData['email']) || empty($userData['password'])) {
             echo json_encode(['success' => false, 'message' => 'Please fill all fields.']);
             exit;
        }
        if ($this->userModel->findUserByUsername($userData['username'])) {
             echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
             exit;
        }
        if ($this->userModel->findUserByEmail($userData['email'])) {
            echo json_encode(['success' => false, 'message' => 'Email is already taken.']);
            exit;
        }

        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        // Generate verification token
        $userData['verification_token'] = bin2hex(random_bytes(50));

        if ($this->userModel->register($userData)) {
            // In a real app, you'd send a verification email here.
            echo json_encode(['success' => true, 'message' => 'Registration successful. Please verify your email.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON.']);
            exit;
        }

        $email = trim($data->email);
        $password = trim($data->password);

        $loggedInUser = $this->userModel->login($email, $password);

        if ($loggedInUser) {
            // In a real app, you would generate a JWT token here for auth.
            // For simplicity, we'll return user data and a success message.
            $this->userModel->updateLastLoginIp($loggedInUser->id, $_SERVER['REMOTE_ADDR']);
            unset($loggedInUser->password); // Don't send password back
            echo json_encode(['success' => true, 'user' => $loggedInUser]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials or email not verified.']);
        }
    }

    public function getOffers() {
        require_once __DIR__ . '/../models/Offer.php';
        $offerModel = new Offer();
        $offers = $offerModel->getActiveOffers();
        echo json_encode(['success' => true, 'offers' => $offers]);
    }

    public function getAds() {
        require_once __DIR__ . '/../models/Ad.php';
        $adModel = new Ad();
        $ads = $adModel->getActiveAds();
        echo json_encode(['success' => true, 'ads' => $ads]);
    }

    public function getDashboardData($userId) {
        require_once __DIR__ . '/../models/OfferwallProvider.php';
        require_once __DIR__ . '/../models/Transaction.php';

        $user = $this->userModel->findUserById($userId);
        unset($user->password); // Ensure password is not sent

        $offerwallProviderModel = new OfferwallProvider();
        $offerwallProviders = $offerwallProviderModel->getActiveProviders();

        $transactionModel = new Transaction();
        $recentTransactions = $transactionModel->getRecentTransactions($userId);

        $referralSummary = $this->userModel->getReferralSummary($userId);

        $dashboardData = [
            'user' => $user,
            'offerwallProviders' => $offerwallProviders,
            'recentTransactions' => $recentTransactions,
            'referralSummary' => $referralSummary
        ];

        echo json_encode(['success' => true, 'data' => $dashboardData]);
    }
}
?>
