<?php
// skillora/admin/login.php

// We are in the /admin/ directory, so we need to go up one level to include files.
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Start a secure session.
secure_session_start();

// If the admin is already logged in, redirect them to the admin dashboard.
if (isset($_SESSION['admin_id'])) {
    redirect('index.php');
}

$error_message = '';

// Handle the form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_message = 'Email and password are required.';
    } else {
        // 1. Find the user by their email address.
        $stmt = $mysqli->prepare("SELECT id, name, password_hash FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            // 2. If a user is found, check if they are an admin.
            $stmt_admin = $mysqli->prepare("SELECT id, role FROM admins WHERE user_id = ? LIMIT 1");
            $stmt_admin->bind_param('i', $user['id']);
            $stmt_admin->execute();
            $admin = $stmt_admin->get_result()->fetch_assoc();
            $stmt_admin->close();

            if ($admin) {
                // 3. Verify the password against the stored hash.
                if (password_verify($password, $user['password_hash'])) {
                    // 4. Login successful. Regenerate session ID to prevent session fixation.
                    session_regenerate_id(true);

                    // Store admin details in the session.
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['admin_name'] = $user['name'];
                    $_SESSION['admin_role'] = $admin['role'];

                    // Update the last_login timestamp.
                    $update_stmt = $mysqli->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                    $update_stmt->bind_param('i', $admin['id']);
                    $update_stmt->execute();
                    $update_stmt->close();

                    // Redirect to the admin dashboard.
                    redirect('index.php');
                } else {
                    // Password does not match.
                    $error_message = 'Invalid email or password.';
                }
            } else {
                // User exists but is not an admin.
                $error_message = 'You do not have permission to access this area.';
            }
        } else {
            // No user found with that email.
            $error_message = 'Invalid email or password.';
        }
    }
}

$page_title = 'Admin Login';
// The paths in header.php use a base_url, so they should work fine from a subdirectory.
require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-login-container" style="max-width: 450px; margin: 5rem auto;">
    <form action="login.php" method="POST" class="styled-form">
        <h2 style="text-align: center;">Admin Panel Login</h2>

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
    </form>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
