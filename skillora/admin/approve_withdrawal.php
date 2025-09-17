<?php
// skillora/admin/approve_withdrawal.php
ob_start();
require_once __DIR__ . '/includes/admin_header.php';
ob_end_clean();

$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($request_id <= 0) {
    redirect('manage_withdrawals.php?status=error&msg=Invalid+request+ID.');
}

// Fetch the request details to ensure it's valid and pending
$stmt = $mysqli->prepare("SELECT * FROM withdrawal_requests WHERE id = ? AND status = 'pending'");
$stmt->bind_param('i', $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) {
    redirect('manage_withdrawals.php?status=error&msg=Request+not+found+or+already+processed.');
}

$user_id = $request['user_id'];
$amount = $request['amount'];

// Begin transaction
$mysqli->begin_transaction();

try {
    // 1. Deduct from user's wallet.
    // The condition `wallet_balance >= ?` is a crucial final check against race conditions.
    $update_wallet_stmt = $mysqli->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ? AND wallet_balance >= ?");
    $update_wallet_stmt->bind_param('did', $amount, $user_id, $amount);
    $update_wallet_stmt->execute();

    // Check if the wallet update was successful
    if ($update_wallet_stmt->affected_rows == 0) {
        throw new Exception('User has insufficient funds, or user not found.');
    }
    $update_wallet_stmt->close();

    // 2. Update the withdrawal request status to 'approved'.
    $update_request_stmt = $mysqli->prepare("UPDATE withdrawal_requests SET status = 'approved' WHERE id = ?");
    $update_request_stmt->bind_param('i', $request_id);
    $update_request_stmt->execute();

    if ($update_request_stmt->affected_rows == 0) {
        throw new Exception('Failed to update the withdrawal request status.');
    }
    $update_request_stmt->close();

    // 3. Log this as a debit transaction
    $log_stmt = $mysqli->prepare("INSERT INTO wallet_transactions (user_id, type, amount, source) VALUES (?, 'debit', ?, 'withdrawal_approved')");
    $log_stmt->bind_param('id', $user_id, $amount);
    $log_stmt->execute();

    if ($log_stmt->affected_rows == 0) {
        throw new Exception('Failed to log the wallet transaction.');
    }
    $log_stmt->close();

    // 4. Create a notification for the user
    $notification_message = "Your withdrawal request for " . number_format($amount, 2) . " NPR has been approved and processed.";
    $notif_stmt = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $notif_stmt->bind_param('is', $user_id, $notification_message);
    $notif_stmt->execute();
    $notif_stmt->close();

    // If all queries were successful, commit the transaction
    $mysqli->commit();
    redirect('manage_withdrawals.php?status=success&msg=' . urlencode('Withdrawal approved and processed.'));

} catch (Exception $e) {
    // If any query failed, roll back all changes
    $mysqli->rollback();
    redirect('manage_withdrawals.php?status=error&msg=' . urlencode('An error occurred: ' . $e->getMessage()));
}

$mysqli->close();
?>
