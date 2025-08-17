<?php
require_once __DIR__ . '/models/User.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    $userModel = new User();
    $user = $userModel->findUserByEmail($email);

    if ($user && $user->verification_token === $token) {
        if ($userModel->verifyEmail($email)) {
            echo 'Your email has been verified successfully. You can now <a href="login.php">login</a>.';
        } else {
            echo 'Failed to verify your email. Please try again.';
        }
    } else {
        echo 'Invalid verification link.';
    }
} else {
    echo 'Invalid request.';
}
?>
