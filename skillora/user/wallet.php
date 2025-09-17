<?php
// skillora/user/wallet.php
$page_title = 'My Wallet';
require_once __DIR__ . '/../includes/header.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    redirect($base_url . '/login.php');
}

$user_id = $_SESSION['user_id'];

// Fetch user's wallet balance
$user_stmt = $mysqli->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();
$wallet_balance = $user['wallet_balance'];

// Fetch transaction history
$trans_stmt = $mysqli->prepare("SELECT type, amount, source, created_at FROM wallet_transactions WHERE user_id = ? ORDER BY created_at DESC");
$trans_stmt->bind_param('i', $user_id);
$trans_stmt->execute();
$transactions = $trans_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$trans_stmt->close();

// Fetch withdrawal request history
$withdrawal_stmt = $mysqli->prepare("SELECT amount, payout_method, status, created_at FROM withdrawal_requests WHERE user_id = ? ORDER BY created_at DESC");
$withdrawal_stmt->bind_param('i', $user_id);
$withdrawal_stmt->execute();
$withdrawals = $withdrawal_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$withdrawal_stmt->close();

$payout_methods = ['eSewa', 'Khalti', 'IME Pay', 'Bank Transfer'];
?>

<section class="wallet-section">
    <div class="container">
        <h2>My Wallet & Withdrawals</h2>
        <div class="wallet-overview">
            <div class="balance-box">
                <h4>Current Balance</h4>
                <p><?php echo number_format($wallet_balance, 2); ?> NPR</p>
            </div>
            <div class="withdrawal-form-box styled-form">
                <form action="handle_withdrawal.php" method="POST">
                    <fieldset>
                        <legend>Request a Withdrawal</legend>
                        <div class="form-group">
                            <label for="amount">Amount to Withdraw</label>
                            <input type="number" name="amount" id="amount" step="0.01" max="<?php echo $wallet_balance; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="payout_method">Payout Method</label>
                            <select name="payout_method" id="payout_method" required>
                                <?php foreach ($payout_methods as $method): ?>
                                    <option value="<?php echo $method; ?>"><?php echo $method; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payout_details">Payout Details</label>
                            <textarea name="payout_details" id="payout_details" rows="3" required placeholder="e.g., Your eSewa ID, Bank Account Number, etc."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </fieldset>
                </form>
            </div>
        </div>

        <div class="transaction-history">
            <h3>Transaction History</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount (NPR)</th>
                        <th>Source / Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="4" style="text-align:center;">No transactions yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr class="<?php echo $t['type']; ?>">
                                <td><?php echo date('M d, Y', strtotime($t['created_at'])); ?></td>
                                <td><?php echo ucfirst($t['type']); ?></td>
                                <td><?php echo number_format($t['amount'], 2); ?></td>
                                <td><?php echo sanitize_output($t['source']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="withdrawal-history" style="margin-top: 3rem;">
            <h3>Withdrawal Request History</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount (NPR)</th>
                        <th>Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($withdrawals)): ?>
                        <tr><td colspan="4" style="text-align:center;">No withdrawal requests made yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($withdrawals as $w): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($w['created_at'])); ?></td>
                                <td><?php echo number_format($w['amount'], 2); ?></td>
                                <td><?php echo sanitize_output($w['payout_method']); ?></td>
                                <td><span class="status-badge <?php echo strtolower(sanitize_output($w['status'])); ?>"><?php echo sanitize_output($w['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
