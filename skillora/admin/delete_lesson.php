<?php
// skillora/admin/delete_lesson.php
ob_start();
require_once __DIR__ . '/includes/admin_header.php';
ob_end_clean();

$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// We get the course_id so we can redirect back to the correct course edit page.
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($lesson_id <= 0 || $course_id <= 0) {
    redirect('manage_courses.php?status=error&msg=' . urlencode('Invalid IDs provided.'));
}

// Prepare and execute the DELETE statement.
$stmt = $mysqli->prepare("DELETE FROM lessons WHERE id = ?");
$stmt->bind_param('i', $lesson_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $message = 'Lesson has been deleted successfully.';
        redirect("edit_course.php?id={$course_id}&status=success&msg=" . urlencode($message));
    } else {
        $message = 'Lesson could not be found or was already deleted.';
        redirect("edit_course.php?id={$course_id}&status=error&msg=" . urlencode($message));
    }
} else {
    $message = 'An error occurred while deleting the lesson.';
    redirect("edit_course.php?id={$course_id}&status=error&msg=" . urlencode($message));
}

$stmt->close();
$mysqli->close();
?>
