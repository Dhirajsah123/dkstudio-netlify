<?php
// skillora/admin/edit_course.php
require_once __DIR__ . '/includes/admin_header.php';

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edit_mode = ($course_id > 0);
$page_title = $edit_mode ? 'Edit Course' : 'Add New Course';

// Default course data structure
$course = [
    'title' => '', 'provider' => '', 'description' => '', 'category_id' => '',
    'level' => 'Beginner', 'language' => 'English', 'tags' => '', 'thumbnail' => ''
];
$course_memberships = [];
$lessons = [];

if ($edit_mode) {
    // Fetch lessons for the course
    $lesson_stmt = $mysqli->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY lesson_order ASC");
    $lesson_stmt->bind_param('i', $course_id);
    $lesson_stmt->execute();
    $lessons = $lesson_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $lesson_stmt->close();

    $stmt = $mysqli->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    } else {
        // Course not found
        redirect('manage_courses.php?status=error&msg=Course+not+found');
    }
    $stmt->close();

    $mem_stmt = $mysqli->prepare("SELECT membership_id FROM course_memberships WHERE course_id = ?");
    $mem_stmt->bind_param('i', $course_id);
    $mem_stmt->execute();
    $result = $mem_stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $course_memberships[] = $row['membership_id'];
    }
    $mem_stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Data Collection & Validation ---
    $title = trim($_POST['title']);
    $provider = trim($_POST['provider']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $level = trim($_POST['level']);
    $language = trim($_POST['language']);
    $tags = trim($_POST['tags']);
    $selected_memberships = $_POST['memberships'] ?? [];

    $errors = [];
    if (empty($title)) $errors[] = 'Title is required.';

    // --- Thumbnail Upload ---
    $thumbnail_name = $course['thumbnail']; // Keep old thumbnail by default
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['thumbnail'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($file['type'], $allowed_types) && $file['size'] < 2000000) { // < 2MB
            // Delete old thumbnail if it exists and is not the default
            if ($edit_mode && !empty($thumbnail_name) && $thumbnail_name != 'default_thumbnail.png') {
                unlink(__DIR__ . '/../uploads/thumbnails/' . $thumbnail_name);
            }
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $thumbnail_name = 'course_' . uniqid() . '.' . $file_extension;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/thumbnails/' . $thumbnail_name);
        } else {
            $errors[] = 'Invalid thumbnail file. Must be JPG, PNG, or GIF and under 2MB.';
        }
    }

    if (empty($errors)) {
        $mysqli->begin_transaction();
        try {
            if ($edit_mode) {
                $sql = "UPDATE courses SET title=?, provider=?, description=?, category_id=?, level=?, language=?, tags=?, thumbnail=? WHERE id=?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('sssissssi', $title, $provider, $description, $category_id, $level, $language, $tags, $thumbnail_name, $course_id);
            } else {
                $sql = "INSERT INTO courses (title, provider, description, category_id, level, language, tags, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('sssissss', $title, $provider, $description, $category_id, $level, $language, $tags, $thumbnail_name);
            }
            $stmt->execute();
            if (!$edit_mode) $course_id = $mysqli->insert_id;
            $stmt->close();

            // --- Update Membership Associations ---
            $del_stmt = $mysqli->prepare("DELETE FROM course_memberships WHERE course_id = ?");
            $del_stmt->bind_param('i', $course_id);
            $del_stmt->execute();
            $del_stmt->close();
            if (!empty($selected_memberships)) {
                $insert_mem_sql = "INSERT INTO course_memberships (course_id, membership_id) VALUES (?, ?)";
                $mem_stmt = $mysqli->prepare($insert_mem_sql);
                foreach ($selected_memberships as $membership_id) {
                    $mem_stmt->bind_param('ii', $course_id, $membership_id);
                    $mem_stmt->execute();
                }
                $mem_stmt->close();
            }

            $mysqli->commit();
            redirect('manage_courses.php?status=success&msg=' . urlencode($edit_mode ? 'Course updated' : 'Course added'));
        } catch (Exception $e) {
            $mysqli->rollback();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch form data
$categories = $mysqli->query("SELECT * FROM course_categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$memberships = $mysqli->query("SELECT * FROM memberships ORDER BY price")->fetch_all(MYSQLI_ASSOC);
?>

<div class="styled-form-wrapper">
    <form action="edit_course.php?id=<?php echo $course_id; ?>" method="POST" enctype="multipart/form-data" class="styled-form">
        <!-- Error Display -->
        <?php if (!empty($errors)): ?>
            <div class="status-box error"><p><?php echo implode('<br>', $errors); ?></p></div>
        <?php endif; ?>

        <fieldset>
            <legend>Course Details</legend>
            <div class="form-group"><label for="title">Course Title</label><input type="text" name="title" id="title" value="<?php echo sanitize_output($course['title']); ?>" required></div>
            <div class="form-group"><label for="provider">Provider</label><input type="text" name="provider" id="provider" value="<?php echo sanitize_output($course['provider']); ?>"></div>
            <div class="form-group"><label for="description">Description</label><textarea name="description" id="description" rows="5"><?php echo sanitize_output($course['description']); ?></textarea></div>
            <div class="form-group"><label for="category_id">Category</label><select name="category_id" id="category_id"><option value="">Select Category</option><?php foreach ($categories as $cat) { echo "<option value='{$cat['id']}' ".($course['category_id']==$cat['id']?'selected':'').">".sanitize_output($cat['name'])."</option>"; } ?></select></div>
            <div class="form-group"><label for="level">Level</label><select name="level" id="level"><?php foreach(['Beginner', 'Intermediate', 'Advanced'] as $lvl) { echo "<option value='$lvl' ".($course['level']==$lvl?'selected':'').">$lvl</option>"; } ?></select></div>
            <div class="form-group"><label for="language">Language</label><input type="text" name="language" id="language" value="<?php echo sanitize_output($course['language']); ?>"></div>
            <div class="form-group"><label for="tags">Tags (comma-separated)</label><input type="text" name="tags" id="tags" value="<?php echo sanitize_output($course['tags']); ?>"></div>
        </fieldset>

        <fieldset><legend>Membership Access</legend><div class="form-group checkbox-group"><?php foreach ($memberships as $mem) { echo "<label class='checkbox-label'><input type='checkbox' name='memberships[]' value='{$mem['id']}' ".(in_array($mem['id'], $course_memberships)?'checked':'')."> ".sanitize_output($mem['name'])."</label>"; } ?></div></fieldset>

        <fieldset><legend>Course Thumbnail</legend><div class="form-group"><label for="thumbnail">Upload New Thumbnail</label><input type="file" name="thumbnail" id="thumbnail"><small>Leave blank to keep the current thumbnail.</small><?php if($edit_mode && !empty($course['thumbnail'])) { echo "<img src='../uploads/thumbnails/{$course['thumbnail']}' alt='Current thumbnail' style='max-width: 200px; margin-top: 10px;'>"; } ?></div></fieldset>

        <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update Course' : 'Add Course'; ?></button>
    </form>

    <?php if ($edit_mode): ?>
    <hr class="form-divider">
    <!-- Lesson Management Section -->
    <div class="styled-form">
        <fieldset>
            <legend>Manage Lessons</legend>

            <table class="admin-table lesson-table">
                <thead><tr><th>Order</th><th>Title</th><th>Duration (mins)</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if(empty($lessons)): ?>
                        <tr><td colspan="4" style="text-align:center;">No lessons yet. Add one below.</td></tr>
                    <?php else: ?>
                        <?php foreach($lessons as $lesson): ?>
                        <tr>
                            <td><?php echo sanitize_output($lesson['lesson_order']); ?></td>
                            <td><?php echo sanitize_output($lesson['title']); ?></td>
                            <td><?php echo sanitize_output($lesson['duration']); ?></td>
                            <td class="actions">
                                <a href="edit_lesson.php?id=<?php echo $lesson['id']; ?>" class="btn-edit">Edit</a>
                                <a href="delete_lesson.php?id=<?php echo $lesson['id']; ?>&course_id=<?php echo $course_id; ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </fieldset>
    </div>

    <hr class="form-divider">
    <!-- Add New Lesson Form -->
    <div class="styled-form">
        <form action="handle_lesson.php" method="POST">
            <fieldset>
                <legend>Add New Lesson</legend>
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="action" value="add">

                <div class="form-group"><label for="lesson_title">Lesson Title</label><input type="text" name="title" id="lesson_title" required></div>
                <div class="form-group"><label for="iframe_url">iFrame URL</label><input type="url" name="iframe_url" id="iframe_url" required></div>
                <div class="form-group"><label for="lesson_order">Order</label><input type="number" name="lesson_order" id="lesson_order" value="<?php echo count($lessons) + 1; ?>" required></div>
                <div class="form-group"><label for="duration">Duration (minutes)</label><input type="number" name="duration" id="duration"></div>

                <button type="submit" class="btn">Add Lesson</button>
            </fieldset>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
