<?php
// skillora/login.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

secure_session_start();

// If a regular user is already logged in, redirect them to their dashboard.
if (isset($_SESSION['user_id'])) {
    redirect('user/dashboard.php');
}
// If an admin is logged in, they should probably use the admin panel.
if (isset($_SESSION['admin_id'])) {
    redirect('admin/index.php');
}

$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_message = 'Email and password are required.';
    } else {
        // Find user by email
        $stmt = $mysqli->prepare("SELECT id, name, password_hash, membership_id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password_hash'])) {
            // Check if this user is also an admin. If so, they should use the admin login.
            $admin_check_stmt = $mysqli->prepare("SELECT id FROM admins WHERE user_id = ?");
            $admin_check_stmt->bind_param('i', $user['id']);
            $admin_check_stmt->execute();
            $is_admin = $admin_check_stmt->get_result()->num_rows > 0;
            $admin_check_stmt->close();

            if ($is_admin) {
                $error_message = 'Administrators should use the admin login page.';
            } else {
                // Login successful: Regenerate session ID and set session variables
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['membership_id'] = $user['membership_id']; // Store membership ID for easy access

                redirect('user/dashboard.php');
            }
        } else {
            $error_message = 'Invalid email or password.';
        }
    }
    $mysqli->close();
}

$page_title = 'Member Login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="user-login-container" style="max-width: 450px; margin: 5rem auto;">
    <form action="login.php" method="POST" class="styled-form">
        <h2 style="text-align: center;">Member Login</h2>

        <?php if (!empty($error_message)): ?>
            <div class="status-box error" style="margin: 1rem 0;">
                <p style="margin: 0;"><?php echo sanitize_output($error_message); ?></p>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required value="<?php echo isset($email) ? sanitize_output($email) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1.1rem;">Login</button>
        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="membership.php">Sign up now</a>.
        </p>
    </form>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
