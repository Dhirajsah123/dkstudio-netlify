<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/RateLimiter.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register() {
        // Process POST data
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Rate limiting
            $ip = $_SERVER['REMOTE_ADDR'];
            if (!RateLimiter::check($ip)) {
                echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again later.']);
                exit;
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // CSRF check
            if (!isset($_POST['csrf_token']) || !CSRF::validateToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
                exit;
            }

            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate data
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            } else {
                if ($this->userModel->findUserByUsername($data['username'])) {
                    $data['username_err'] = 'Username is already taken';
                }
            }

            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }

            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Make sure errors are empty
            if (empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Generate verification token
                $data['verification_token'] = bin2hex(random_bytes(50));

                // Register user
                if ($this->userModel->register($data)) {
                    // Send verification email (to be implemented)
                    $this->sendVerificationEmail($data['email'], $data['verification_token']);

                    echo json_encode(['success' => true, 'message' => 'Registration successful. Please check your email to verify your account.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
                }
            } else {
                // Return errors
                echo json_encode(['success' => false, 'errors' => $data]);
            }
        }
    }

    public function sendVerificationEmail($email, $token) {
        // In a real application, you would use a library like PHPMailer to send an email.
        // For now, we will just log the verification link to a file.
        $verification_link = BASE_URL . '/php/verify.php?email=' . $email . '&token=' . $token;
        error_log("Verification link for $email: $verification_link\n", 3, "verification_links.log");
    }

    public function login() {
        // Process POST data
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Rate limiting
            $ip = $_SERVER['REMOTE_ADDR'];
            if (!RateLimiter::check($ip)) {
                echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again later.']);
                exit;
            }

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // CSRF check
            if (!isset($_POST['csrf_token']) || !CSRF::validateToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
                exit;
            }

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => '',
            ];

            // Validate data
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for user/email
            if ($this->userModel->findUserByEmail($data['email'])) {
                // User found
            } else {
                // User not found
                $data['email_err'] = 'No user found';
            }

            // Make sure errors are empty
            if (empty($data['email_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    // Update last login IP
                    $this->userModel->updateLastLoginIp($loggedInUser->id, $_SERVER['REMOTE_ADDR']);
                    // Create session
                    $this->createUserSession($loggedInUser);
                    echo json_encode(['success' => true]);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    echo json_encode(['success' => false, 'errors' => $data]);
                }
            } else {
                // Return errors
                echo json_encode(['success' => false, 'errors' => $data]);
            }
        }
    }

    public function createUserSession($user) {
        session_start();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->username;
    }

    public function logout() {
        session_start();
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        // redirect('pages/login');
    }
}

// Basic routing
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $controller = new AuthController();
    if (method_exists($controller, $action)) {
        $controller->$action();
    }
}
?>
