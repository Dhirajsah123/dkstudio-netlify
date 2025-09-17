<?php
// skillora/user/dashboard.php
$page_title = 'My Dashboard';
require_once __DIR__ . '/../includes/header.php';

// Authentication check. Although the login flow handles this,
// it's good practice to have it on protected pages.
if (!isset($_SESSION['user_id'])) {
    redirect($base_url . '/login.php?status=error&msg=Please+log+in+to+access+your+dashboard.');
}

$user_id = $_SESSION['user_id'];

// Fetch comprehensive user data for the dashboard
$stmt = $mysqli->prepare(
    "SELECT u.name, u.email, u.referral_code, u.wallet_balance, u.membership_expiry_date, m.name as membership_name
     FROM users u
     LEFT JOIN memberships m ON u.membership_id = m.id
     WHERE u.id = ?"
);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    // This case would be rare, but good to handle (e.g., user deleted while logged in)
    redirect($base_url . '/logout.php');
}

$is_expired = $user['membership_expiry_date'] && (new DateTime() > new DateTime($user['membership_expiry_date']));

// Fetch unread notifications
$notif_stmt = $mysqli->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
$notif_stmt->bind_param('i', $user_id);
$notif_stmt->execute();
$notifications = $notif_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$notif_stmt->close();

// Fetch referral count
$ref_count_stmt = $mysqli->prepare("SELECT COUNT(*) as referral_count FROM users WHERE referred_by = ?");
$ref_count_stmt->bind_param('i', $user_id);
$ref_count_stmt->execute();
$referral_data = $ref_count_stmt->get_result()->fetch_assoc();
$ref_count_stmt->close();
$referral_count = $referral_data['referral_count'];

?>
<section class="user-dashboard-section">
    <div class="container">
        <h2>Welcome, <?php echo sanitize_output($user['name']); ?>!</h2>
        <p>This is your personal dashboard. Here you can manage your account and access your courses.</p>

        <?php if (!empty($notifications)): ?>
        <div class="notifications-container">
            <h3>Notifications</h3>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li><?php echo sanitize_output($notification['message']); ?> <small>(<?php echo date('M d', strtotime($notification['created_at'])); ?>)</small></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="dashboard-widgets">
            <div class="widget">
                <h3>Membership Plan</h3>
                <p class="widget-value"><?php echo $user['membership_name'] ? sanitize_output($user['membership_name']) : 'No Active Plan'; ?></p>
                <?php if ($user['membership_expiry_date']): ?>
                    <p class="expiry-date <?php if($is_expired) echo 'expired'; ?>">
                        Expires on: <?php echo date('F j, Y', strtotime($user['membership_expiry_date'])); ?>
                        <?php if($is_expired) echo '(Expired)'; ?>
                    </p>
                <?php endif; ?>
                <a href="<?php echo $base_url; ?>/membership.php" class="btn">
                    <?php echo $user['membership_id'] ? 'Renew / Upgrade' : 'Get a Plan'; ?>
                </a>
            </div>
            <div class="widget">
                <h3>Wallet Balance</h3>
                <p class="widget-value"><?php echo number_format($user['wallet_balance'], 2); ?> NPR</p>
                <a href="<?php echo $base_url; ?>/user/wallet.php" class="btn">Manage Wallet</a>
            </div>
            <div class="widget">
                <h3>Total Referrals</h3>
                <p class="widget-value"><?php echo $referral_count; ?></p>
                <a href="#" class="btn">View Details</a>
            </div>
            <div class="widget referral-widget">
                <h3>Your Referral Code</h3>
                <p>Share this code with friends! You'll earn a 10% bonus when they buy a membership.</p>
                <input type="text" value="<?php echo sanitize_output($user['referral_code']); ?>" readonly id="ref-code-input">
                <button class="btn" id="copy-ref-code">Copy Code</button>
            </div>
        </div>

        <div class="dashboard-links">
            <a href="<?php echo $base_url; ?>/courses.php" class="dashboard-link-card">
                <h4>My Courses</h4>
                <p>View all the courses available with your membership.</p>
            </a>
            <a href="<?php echo $base_url; ?>/user/profile.php" class="dashboard-link-card">
                <h4>Profile Settings</h4>
                <p>Update your name and change your password.</p>
            </a>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
