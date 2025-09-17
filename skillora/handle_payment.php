<?php
// skillora/handle_payment.php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Define base URL here or ensure it's included from a config file
$base_url = '/skillora';

// 1. Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base_url . '/membership.php');
}

secure_session_start();

// 2. Basic Input Validation
$errors = [];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$membership_id = $_POST['selected_membership_id'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
// Use null coalescing operator for optional field
$referrer_code = !empty(trim($_POST['referrer_code'])) ? trim($_POST['referrer_code']) : null;

if (empty($name)) $errors[] = 'Name is required.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
if (empty($membership_id)) $errors[] = 'Please select a membership plan.';
if (empty($payment_method)) $errors[] = 'Please select a payment method.';

// 3. File Upload Handling
$receipt_file_name = '';
if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['receipt'];
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    // More robust type checking using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $file_type = $finfo->file($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        $errors[] = 'Invalid file type. Only JPG, PNG, and PDF are allowed.';
    }

    if ($file['size'] > $max_size) {
        $errors[] = 'File is too large. Maximum size is 5MB.';
    }

    if (empty($errors)) {
        // Generate a unique filename to prevent overwrites
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $receipt_file_name = uniqid('receipt_', true) . '.' . $file_extension;
        $upload_path = __DIR__ . '/uploads/receipts/' . $receipt_file_name;

        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            $errors[] = 'There was an error uploading your receipt. Please try again.';
        }
    }
} else {
    $errors[] = 'Payment receipt is required and must be uploaded successfully.';
}

// 4. If there are errors, redirect back with the error message
if (!empty($errors)) {
    $error_message = urlencode(implode('<br>', $errors));
    redirect("status.php?type=error&message={$error_message}");
}

// 5. Insert into Database using Prepared Statements
$sql = "INSERT INTO pending_payments (name, email, selected_membership_id, payment_method, receipt_file, referrer_code) VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
if ($stmt) {
    // Bind parameters: s = string, i = integer
    $stmt->bind_param('ssisss', $name, $email, $membership_id, $payment_method, $receipt_file_name, $referrer_code);

    if ($stmt->execute()) {
        // Success! Redirect to a thank you page.
        $stmt->close();
        $mysqli->close();
        $success_message = urlencode("Thank you! Your application has been submitted and is pending review. You will be notified via email once it's approved.");
        redirect("status.php?type=success&message={$success_message}");
    } else {
        // Database execution error
        $error_message = urlencode("An error occurred while submitting your application. Please contact support if this issue persists.");
        redirect("status.php?type=error&message={$error_message}");
    }
} else {
    // Database prepare statement error
    $error_message = urlencode("A critical server error occurred. Please contact support.");
    redirect("status.php?type=error&message={$error_message}");
}
?>
