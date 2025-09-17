<?php
// skillora/admin/manage_withdrawals.php
$page_title = 'Manage Withdrawals';
require_once __DIR__ . '/includes/admin_header.php';

// Fetch pending withdrawal requests along with user details
$sql = "SELECT wr.*, u.name as user_name, u.email as user_email, u.wallet_balance
        FROM withdrawal_requests wr
        JOIN users u ON wr.user_id = u.id
        WHERE wr.status = 'pending'
        ORDER BY wr.created_at ASC";
$result = $mysqli->query($sql);
$requests = $result->fetch_all(MYSQLI_ASSOC);

?>

<section class="manage-withdrawals-section">
    <div class="container-fluid">
        <p>Review and process pending withdrawal requests from users.</p>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Amount Requested</th>
                        <th>Current Balance</th>
                        <th>Payout Method</th>
                        <th>Payout Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No pending withdrawal requests.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                                <td>
                                    <?php echo sanitize_output($request['user_name']); ?><br>
                                    <small><?php echo sanitize_output($request['user_email']); ?></small>
                                </td>
                                <td><strong><?php echo number_format($request['amount'], 2); ?> NPR</strong></td>
                                <td><?php echo number_format($request['wallet_balance'], 2); ?> NPR</td>
                                <td><?php echo sanitize_output($request['payout_method']); ?></td>
                                <td><?php echo nl2br(sanitize_output($request['payout_details'])); ?></td>
                                <td class="actions">
                                    <a href="approve_withdrawal.php?id=<?php echo $request['id']; ?>" class="btn-approve" onclick="return confirm('Are you sure you want to approve this withdrawal? This will deduct the amount from the user\'s wallet.');">Approve</a>
                                    <a href="reject_withdrawal.php?id=<?php echo $request['id']; ?>" class="btn-reject" onclick="return confirm('Are you sure you want to reject this request?');">Reject</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
$mysqli->close();
require_once __DIR__ . '/includes/admin_footer.php';
?>
