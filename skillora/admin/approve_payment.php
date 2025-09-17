<?php
// skillora/admin/approve_payment.php

// The admin_header includes the DB connection, functions, and session/auth check.
// We include it to protect this page, but we won't be displaying any HTML from it.
ob_start(); // Start output buffering to prevent any output from the header.
require_once __DIR__ . '/includes/admin_header.php';
ob_end_clean(); // Discard the output buffer.

// --- Main Logic ---

// 1. Get Payment ID and Admin ID
$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$admin_id = $_SESSION['admin_id'];

if ($payment_id <= 0) {
    redirect('index.php?status=error&msg=' . urlencode('Invalid Payment ID.'));
}

// 2. Fetch Pending Payment Data
$stmt = $mysqli->prepare("SELECT * FROM pending_payments WHERE id = ? AND status = 'pending'");
$stmt->bind_param('i', $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    redirect('index.php?status=error&msg=' . urlencode('Payment not found or already processed.'));
}

// 3. Begin Transaction
$mysqli->begin_transaction();

try {
    // 4. Get membership details
    $mem_stmt = $mysqli->prepare("SELECT name, duration_days, price FROM memberships WHERE id = ?");
    $mem_stmt->bind_param('i', $payment['selected_membership_id']);
    $mem_stmt->execute();
    $membership = $mem_stmt->get_result()->fetch_assoc();
    $duration_days = $membership['duration_days'];
    $mem_price = $membership['price'];
    $membership_name = $membership['name'];
    $mem_stmt->close();

    $expiry_date = date('Y-m-d', strtotime("+{$duration_days} days"));

    // 5. Check if user already exists
    $user_stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $user_stmt->bind_param('s', $payment['email']);
    $user_stmt->execute();
    $existing_user = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();

    $affected_user_id = null;

    if ($existing_user) {
        // User exists: Update their membership
        $affected_user_id = $existing_user['id'];
        $update_user_sql = "UPDATE users SET membership_id = ?, membership_expiry_date = ? WHERE id = ?";
        $update_user_stmt = $mysqli->prepare($update_user_sql);
        $update_user_stmt->bind_param('isi', $payment['selected_membership_id'], $expiry_date, $affected_user_id);
        $update_user_stmt->execute();
        if ($update_user_stmt->affected_rows == 0) throw new Exception("Failed to update user membership.");
        $update_user_stmt->close();
    } else {
        // User does not exist: Create a new user
        $password = bin2hex(random_bytes(8));
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $referral_code = 'REF-' . strtoupper(bin2hex(random_bytes(4)));
        $referred_by_id = null;

        if (!empty($payment['referrer_code'])) {
            $ref_stmt = $mysqli->prepare("SELECT id FROM users WHERE referral_code = ?");
            $ref_stmt->bind_param('s', $payment['referrer_code']);
            $ref_stmt->execute();
            if ($ref_row = $ref_stmt->get_result()->fetch_assoc()) {
                $referred_by_id = $ref_row['id'];
            }
            $ref_stmt->close();
        }

        $insert_user_sql = "INSERT INTO users (name, email, password_hash, membership_id, membership_expiry_date, referral_code, referred_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_user_stmt = $mysqli->prepare($insert_user_sql);
        $insert_user_stmt->bind_param('sssisisi', $payment['name'], $payment['email'], $password_hash, $payment['selected_membership_id'], $expiry_date, $referral_code, $referred_by_id);
        $insert_user_stmt->execute();
        $affected_user_id = $insert_user_stmt->insert_id;
        if ($affected_user_id == 0) throw new Exception("Failed to create new user account.");
        $insert_user_stmt->close();
    }

    // 6. Handle Referral Bonus
    if (!empty($payment['referrer_code'])) {
        $ref_user_stmt = $mysqli->prepare("SELECT id FROM users WHERE referral_code = ?");
        $ref_user_stmt->bind_param('s', $payment['referrer_code']);
        $ref_user_stmt->execute();
        $referrer = $ref_user_stmt->get_result()->fetch_assoc();
        $ref_user_stmt->close();

        if ($referrer) {
            $referrer_id = $referrer['id'];
            $bonus_amount = $mem_price * 0.10;
            $wallet_sql = "UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?";
            $wallet_stmt = $mysqli->prepare($wallet_sql);
            $wallet_stmt->bind_param('di', $bonus_amount, $referrer_id);
            $wallet_stmt->execute();
            if ($wallet_stmt->affected_rows == 0) throw new Exception("Failed to update referrer wallet.");
            $wallet_stmt->close();

            $trans_sql = "INSERT INTO wallet_transactions (user_id, type, amount, source) VALUES (?, 'credit', ?, 'referral_bonus')";
            $trans_stmt = $mysqli->prepare($trans_sql);
            $trans_stmt->bind_param('id', $referrer_id, $bonus_amount);
            $trans_stmt->execute();
            if ($trans_stmt->affected_rows == 0) throw new Exception("Failed to log wallet transaction.");
            $trans_stmt->close();
        }
    }

    // 7. Update pending_payments table
    $update_sql = "UPDATE pending_payments SET status = 'approved', approved_at = NOW(), admin_id = ? WHERE id = ?";
    $update_stmt = $mysqli->prepare($update_sql);
    $update_stmt->bind_param('ii', $admin_id, $payment_id);
    $update_stmt->execute();
    if ($update_stmt->affected_rows == 0) throw new Exception("Failed to update payment status.");
    $update_stmt->close();

    // 8. Create a notification for the user
    $notification_message = "Your payment for the " . htmlspecialchars($membership_name) . " membership has been approved. Your membership is active until " . date('F j, Y', strtotime($expiry_date)) . ".";
    $notif_stmt = $mysqli->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $notif_stmt->bind_param('is', $affected_user_id, $notification_message);
    $notif_stmt->execute();
    $notif_stmt->close();

    // 9. Commit transaction
    $mysqli->commit();
    redirect('index.php?status=success&msg=' . urlencode('Payment approved successfully. User account created/updated.'));

} catch (Exception $e) {
    // 10. Rollback on error
    $mysqli->rollback();
    redirect('index.php?status=error&msg=' . urlencode('An error occurred: ' . $e->getMessage()));
}

$mysqli->close();
?>
