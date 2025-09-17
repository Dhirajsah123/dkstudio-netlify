<?php
// skillora/admin/delete_course.php
ob_start();
require_once __DIR__ . '/includes/admin_header.php';
ob_end_clean();

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($course_id <= 0) {
    redirect('manage_courses.php?status=error&msg=' . urlencode('Invalid course ID.'));
}

// 1. Fetch the thumbnail filename before deleting the record.
$stmt = $mysqli->prepare("SELECT thumbnail FROM courses WHERE id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$thumbnail_to_delete = $course ? $course['thumbnail'] : null;
$stmt->close();

if (!$course) {
    redirect('manage_courses.php?status=error&msg=' . urlencode('Course not found.'));
}

// 2. Delete the course from the database.
// ON DELETE CASCADE will handle lessons and course_memberships.
$delete_stmt = $mysqli->prepare("DELETE FROM courses WHERE id = ?");
$delete_stmt->bind_param('i', $course_id);

if ($delete_stmt->execute()) {
    // 3. If DB deletion is successful, delete the thumbnail file.
    if ($thumbnail_to_delete && $thumbnail_to_delete !== 'default_thumbnail.png') {
        $thumbnail_path = __DIR__ . '/../uploads/thumbnails/' . $thumbnail_to_delete;
        if (file_exists($thumbnail_path)) {
            unlink($thumbnail_path);
        }
    }
    redirect('manage_courses.php?status=success&msg=' . urlencode('Course and all associated lessons have been deleted.'));
} else {
    redirect('manage_courses.php?status=error&msg=' . urlencode('Failed to delete the course.'));
}

$delete_stmt->close();
$mysqli->close();
?>
