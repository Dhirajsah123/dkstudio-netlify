<?php
// skillora/admin/handle_lesson.php
ob_start();
require_once __DIR__ . '/includes/admin_header.php';
ob_end_clean();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('manage_courses.php');
}

$action = $_POST['action'] ?? '';
$course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
$lesson_id = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : 0;

// --- Form data ---
$title = trim($_POST['title'] ?? '');
$iframe_url = trim($_POST['iframe_url'] ?? '');
$lesson_order = (int)($_POST['lesson_order'] ?? 0);
$duration = (int)($_POST['duration'] ?? 0);

// --- Validation ---
$errors = [];
if (empty($title)) $errors[] = 'Lesson title is required.';
if (empty($iframe_url) || !filter_var($iframe_url, FILTER_VALIDATE_URL)) {
    $errors[] = 'A valid iframe URL is required.';
}
if ($course_id <= 0) {
    $errors[] = 'Invalid course specified.';
}

if (!empty($errors)) {
    // Redirect back with error
    $error_msg = urlencode(implode('<br>', $errors));
    redirect("edit_course.php?id={$course_id}&status=error&msg={$error_msg}");
}

// --- Database Operation ---
if ($action === 'add') {
    $sql = "INSERT INTO lessons (course_id, title, iframe_url, lesson_order, duration) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('issii', $course_id, $title, $iframe_url, $lesson_order, $duration);
    $success_msg = 'Lesson added successfully.';
    $error_msg = 'Failed to add lesson.';

} elseif ($action === 'edit' && $lesson_id > 0) {
    $sql = "UPDATE lessons SET title = ?, iframe_url = ?, lesson_order = ?, duration = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssiii', $title, $iframe_url, $lesson_order, $duration, $lesson_id);
    $success_msg = 'Lesson updated successfully.';
    $error_msg = 'Failed to update lesson.';

} else {
    redirect("edit_course.php?id={$course_id}&status=error&msg=Invalid+action.");
}

if ($stmt->execute()) {
    redirect("edit_course.php?id={$course_id}&status=success&msg=" . urlencode($success_msg));
} else {
    redirect("edit_course.php?id={$course_id}&status=error&msg=" . urlencode($error_msg));
}

$stmt->close();
$mysqli->close();
?>
