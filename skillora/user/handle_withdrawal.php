<?php
// skillora/user/handle_withdrawal.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

secure_session_start();

// Authentication check
if (!isset($_SESSION['user_id'])) {
    redirect($base_url . '/login.php');
}

// Check for POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base_url . '/user/wallet.php');
}

$user_id = $_SESSION['user_id'];
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
$payout_method = trim($_POST['payout_method'] ?? '');
$payout_details = trim($_POST['payout_details'] ?? '');

// --- Validation ---
$errors = [];
if ($amount <= 0) {
    $errors[] = 'Withdrawal amount must be a positive number.';
}
if (empty($payout_method)) {
    $errors[] = 'Please select a payout method.';
}
if (empty($payout_details)) {
    $errors[] = 'Please provide your payout details.';
}

// Check if user has sufficient balance
$stmt = $mysqli->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || $amount > $user['wallet_balance']) {
    $errors[] = 'You do not have sufficient balance to make this withdrawal.';
}

if (!empty($errors)) {
    $error_msg = urlencode(implode('<br>', $errors));
    redirect("wallet.php?status=error&msg={$error_msg}");
}

// --- Database Insert ---
$sql = "INSERT INTO withdrawal_requests (user_id, amount, payout_method, payout_details, status) VALUES (?, ?, ?, ?, 'pending')";
$insert_stmt = $mysqli->prepare($sql);
$insert_stmt->bind_param('idss', $user_id, $amount, $payout_method, $payout_details);

if ($insert_stmt->execute()) {
    $success_msg = urlencode('Your withdrawal request has been submitted successfully and is pending review.');
    redirect("wallet.php?status=success&msg={$success_msg}");
} else {
    $error_msg = urlencode('A database error occurred. Please try again.');
    redirect("wallet.php?status=error&msg={$error_msg}");
}

$insert_stmt->close();
$mysqli->close();
?>
