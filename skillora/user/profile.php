<?php
// skillora/user/profile.php
$page_title = 'My Profile';
require_once __DIR__ . '/../includes/header.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    redirect($base_url . '/login.php');
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle password change form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_new_password'];

    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'Please fill in all password fields.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 8) {
        $error_message = 'New password must be at least 8 characters long.';
    } else {
        // Fetch current password hash
        $stmt = $mysqli->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Verify current password
        if ($result && password_verify($current_password, $result['password_hash'])) {
            // Hash the new password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the database
            $update_stmt = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $update_stmt->bind_param('si', $new_password_hash, $user_id);
            if ($update_stmt->execute()) {
                $success_message = 'Your password has been updated successfully.';
            } else {
                $error_message = 'An error occurred while updating your password.';
            }
            $update_stmt->close();
        } else {
            $error_message = 'Incorrect current password.';
        }
    }
}

// Fetch user's current info to display
$stmt = $mysqli->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>

<section class="profile-section">
    <div class="container">
        <h2>Profile Settings</h2>
        <p>View and manage your account details.</p>

        <div class="profile-forms-container">
            <!-- Display User Info -->
            <div class="styled-form">
                <fieldset>
                    <legend>Your Information</legend>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" value="<?php echo sanitize_output($user['name']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo sanitize_output($user['email']); ?>" readonly>
                    </div>
                </fieldset>
            </div>

            <!-- Change Password Form -->
            <div class="styled-form" style="margin-top: 2rem;">
                <form action="profile.php" method="POST">
                    <fieldset>
                        <legend>Change Password</legend>

                        <?php if ($error_message): ?>
                            <div class="status-box error"><p><?php echo $error_message; ?></p></div>
                        <?php endif; ?>
                        <?php if ($success_message): ?>
                            <div class="status-box success"><p><?php echo $success_message; ?></p></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" name="current_password" id="current_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" name="new_password" id="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_new_password">Confirm New Password</label>
                            <input type="password" name="confirm_new_password" id="confirm_new_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
