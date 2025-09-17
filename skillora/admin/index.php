<?php
// skillora/admin/index.php
$page_title = 'Admin Dashboard';
require_once __DIR__ . '/includes/admin_header.php';

// Fetch pending payments. We join with the memberships table to also get the plan name.
$sql = "SELECT pp.id, pp.name, pp.email, pp.submitted_at, m.name as membership_name
        FROM pending_payments pp
        JOIN memberships m ON pp.selected_membership_id = m.id
        WHERE pp.status = 'pending'
        ORDER BY pp.submitted_at ASC";

$result = $mysqli->query($sql);
$pending_payments = $result->fetch_all(MYSQLI_ASSOC);
$mysqli->close();
?>

<section class="dashboard-overview">
    <p>Review the following payment submissions. Approving a payment will automatically create a user account and assign the membership.</p>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Submitted At</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Membership Plan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pending_payments)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">No pending payments found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_payments as $payment): ?>
                        <tr>
                            <td><?php echo sanitize_output(date('M d, Y, g:i A', strtotime($payment['submitted_at']))); ?></td>
                            <td><?php echo sanitize_output($payment['name']); ?></td>
                            <td><a href="mailto:<?php echo sanitize_output($payment['email']); ?>"><?php echo sanitize_output($payment['email']); ?></a></td>
                            <td><?php echo sanitize_output($payment['membership_name']); ?></td>
                            <td class="actions">
                                <!-- These will link to a script that handles the status change.
                                     A confirmation step (e.g., via JavaScript) is recommended for production. -->
                                <a href="approve_payment.php?id=<?php echo $payment['id']; ?>" class="btn-approve">Approve</a>
                                <a href="reject_payment.php?id=<?php echo $payment['id']; ?>" class="btn-reject">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>
