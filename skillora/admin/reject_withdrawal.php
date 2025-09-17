<?php
// skillora/admin/reject_withdrawal.php
ob_start();
require_once __DIR__ . '/includes/admin_header.php';
ob_end_clean();

$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($request_id <= 0) {
    redirect('manage_withdrawals.php?status=error&msg=Invalid+request+ID.');
}

$mysqli->begin_transaction();

try {
    // 1. Fetch request details to get user_id and amount for the notification
    $stmt = $mysqli->prepare("SELECT user_id, amount FROM withdrawal_requests WHERE id = ? AND status = 'pending'");
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$request) {
        throw new Exception('Request not found or already processed.');
    }

    // 2. Update the request status to 'rejected'
    $update_stmt = $mysqli->prepare("UPDATE withdrawal_requests SET status = 'rejected' WHERE id = ?");
    $update_stmt->bind_param('i', $request_id);
    $update_stmt->execute();
    if ($update_stmt->affected_rows == 0) {
        throw new Exception('Failed to update withdrawal request status.');
    }
    $update_stmt->close();

    // 3. Create a notification for the user
    $user_id = $request['user_id'];
    $amount = $request['amount'];
    $notification_message = "Your withdrawal request for " . number_format($amount, 2) . " NPR has been rejected. Please contact support if you have questions.";
    $notif_stmt = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $notif_stmt->bind_param('is', $user_id, $notification_message);
    $notif_stmt->execute();
    $notif_stmt->close();

    // 4. Commit the transaction
    $mysqli->commit();
    redirect('manage_withdrawals.php?status=success&msg=' . urlencode('Withdrawal request has been rejected.'));

} catch (Exception $e) {
    $mysqli->rollback();
    redirect('manage_withdrawals.php?status=error&msg=' . urlencode($e->getMessage()));
}

$mysqli->close();
?>
