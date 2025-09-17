<?php
// skillora/admin/edit_lesson.php
$page_title = 'Edit Lesson';
require_once __DIR__ . '/includes/admin_header.php';

$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($lesson_id <= 0) {
    redirect('manage_courses.php?status=error&msg=Invalid+lesson+ID');
}

// Fetch existing lesson data
$stmt = $mysqli->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->bind_param('i', $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
$lesson = $result->fetch_assoc();
$stmt->close();

if (!$lesson) {
    redirect('manage_courses.php?status=error&msg=Lesson+not+found');
}

$course_id = $lesson['course_id']; // For redirecting back

?>

<div class="styled-form-wrapper">
    <form action="handle_lesson.php" method="POST" class="styled-form">
        <fieldset>
            <legend>Editing Lesson: <?php echo sanitize_output($lesson['title']); ?></legend>

            <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <input type="hidden" name="action" value="edit">

            <div class="form-group">
                <label for="title">Lesson Title</label>
                <input type="text" name="title" id="title" value="<?php echo sanitize_output($lesson['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="iframe_url">iFrame URL</label>
                <input type="url" name="iframe_url" id="iframe_url" value="<?php echo sanitize_output($lesson['iframe_url']); ?>" required>
            </div>
            <div class="form-group">
                <label for="lesson_order">Order</label>
                <input type="number" name="lesson_order" id="lesson_order" value="<?php echo sanitize_output($lesson['lesson_order']); ?>" required>
            </div>
            <div class="form-group">
                <label for="duration">Duration (minutes)</label>
                <input type="number" name="duration" id="duration" value="<?php echo sanitize_output($lesson['duration']); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Update Lesson</button>
            <a href="edit_course.php?id=<?php echo $course_id; ?>" class="btn">Cancel</a>
        </fieldset>
    </form>
</div>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>
