<?php
// skillora/admin/manage_courses.php
$page_title = 'Manage Courses';
require_once __DIR__ . '/includes/admin_header.php';

// Fetch all courses with their category names
$sql = "SELECT c.*, cc.name as category_name
        FROM courses c
        LEFT JOIN course_categories cc ON c.category_id = cc.id
        ORDER BY c.title ASC";
$result = $mysqli->query($sql);
$courses = $result->fetch_all(MYSQLI_ASSOC);

?>

<section class="manage-courses-section">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p>Here you can add, edit, or delete course listings.</p>
            <a href="edit_course.php" class="btn btn-success">Add New Course</a>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Provider</th>
                        <th>Level</th>
                        <th>Language</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No courses found. Add one to get started!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><strong><?php echo sanitize_output($course['title']); ?></strong></td>
                                <td><?php echo sanitize_output($course['category_name'] ?? 'N/A'); ?></td>
                                <td><?php echo sanitize_output($course['provider']); ?></td>
                                <td><?php echo sanitize_output($course['level']); ?></td>
                                <td><?php echo sanitize_output($course['language']); ?></td>
                                <td class="actions">
                                    <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete_course.php?id=<?php echo $course['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this course and all its lessons? This action cannot be undone.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php
$mysqli->close();
require_once __DIR__ . '/includes/admin_footer.php';
?>
