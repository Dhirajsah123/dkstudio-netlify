<?php
// skillora/status.php
$page_title = 'Application Status';
require_once __DIR__ . '/includes/header.php';

// Get message and type from URL, with defaults for security
$message = isset($_GET['message']) ? urldecode($_GET['message']) : 'An unexpected error occurred.';
$type = isset($_GET['type']) && $_GET['type'] === 'error' ? 'error' : 'success'; // Default to 'success'

// Sanitize the message for display. Even though it's URL encoded, this is a good final check.
$sanitized_message = sanitize_output($message);
// Since the message might contain my own <br> tags for multiple errors, I will allow them.
// A better long-term solution might be to pass errors as an array.
$formatted_message = nl2br($sanitized_message);


?>

<section class="status-section" style="padding: 4rem 0;">
    <div class="status-box <?php echo $type; ?>">
        <h2><?php echo $type === 'success' ? 'Success!' : 'An Error Occurred'; ?></h2>
        <p><?php echo $formatted_message; ?></p>
        <a href="<?php echo $base_url; ?>/index.php" class="btn">Return to Homepage</a>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
