<?php
// skillora/admin/reject_payment.php
ob_start();
require_once __DIR__ . '/includes/admin_header.php';
ob_end_clean();

$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$admin_id = $_SESSION['admin_id'];

if ($payment_id <= 0) {
    redirect('index.php?status=error&msg=' . urlencode('Invalid Payment ID provided.'));
}

$mysqli->begin_transaction();

try {
    // 1. Fetch payment details to get the user's email
    $stmt = $mysqli->prepare("SELECT email FROM pending_payments WHERE id = ? AND status = 'pending'");
    $stmt->bind_param('i', $payment_id);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$payment) {
        throw new Exception('Payment not found or already processed.');
    }

    // 2. Update the payment status to 'rejected'
    $update_stmt = $mysqli->prepare("UPDATE pending_payments SET status = 'rejected', admin_id = ? WHERE id = ?");
    $update_stmt->bind_param('ii', $admin_id, $payment_id);
    $update_stmt->execute();
    if ($update_stmt->affected_rows == 0) {
        throw new Exception('Failed to update payment status.');
    }
    $update_stmt->close();

    // 3. Check if a user exists with this email to send a notification
    $user_stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $user_stmt->bind_param('s', $payment['email']);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();

    if ($user) {
        // 4. Create a notification for the user
        $user_id = $user['id'];
        $notification_message = "Your recent membership payment submission has been rejected. Please contact support for more details.";
        $notif_stmt = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif_stmt->bind_param('is', $user_id, $notification_message);
        $notif_stmt->execute();
        $notif_stmt->close();
    }

    $mysqli->commit();
    redirect('index.php?status=success&msg=' . urlencode('Payment has been rejected.'));

} catch (Exception $e) {
    $mysqli->rollback();
    redirect('index.php?status=error&msg=' . urlencode($e->getMessage()));
}

$mysqli->close();
?>
