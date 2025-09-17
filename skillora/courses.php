<?php
// skillora/courses.php
$page_title = 'Our Courses';
require_once __DIR__ . '/includes/header.php';

$courses = [];
$user_is_logged_in = isset($_SESSION['user_id']);

if ($user_is_logged_in) {
    // User is logged in, fetch courses they have access to via the pivot table.
    $user_id = $_SESSION['user_id'];
    $stmt = $mysqli->prepare(
        "SELECT DISTINCT c.* FROM courses c
         JOIN course_memberships cm ON c.id = cm.course_id
         JOIN users u ON cm.membership_id = u.membership_id
         WHERE u.id = ?
         ORDER BY c.title ASC"
    );
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Guest user: show all courses as a preview.
    $result = $mysqli->query("SELECT * FROM courses ORDER BY title ASC");
    $courses = $result->fetch_all(MYSQLI_ASSOC);
}

?>

<section class="course-catalog-section">
    <div class="container">
        <h2 class="section-title">Explore Our Courses</h2>

        <?php if ($user_is_logged_in && empty($courses)): ?>
            <div class="status-box" style="border-color: var(--primary-color); background-color: #eef5ff; text-align: center;">
                <h3>No Courses Available</h3>
                <p>Your current membership plan does not include any courses, or we haven't added them yet. Please check back later or consider upgrading your plan.</p>
                <a href="<?php echo $base_url; ?>/membership.php" class="btn">View Membership Plans</a>
            </div>
        <?php endif; ?>

        <div class="course-grid">
            <?php foreach ($courses as $course): ?>
                <div class="course-card">
                    <img src="<?php echo $base_url; ?>/assets/images/<?php echo sanitize_output($course['thumbnail']); ?>" alt="<?php echo sanitize_output($course['title']); ?>" class="course-thumbnail">
                    <div class="course-content">
                        <h3><?php echo sanitize_output($course['title']); ?></h3>
                        <p class="course-provider"><?php echo sanitize_output($course['provider']); ?></p>
                        <p class="course-description"><?php echo substr(sanitize_output($course['description']), 0, 100); ?>...</p>

                        <?php if ($user_is_logged_in): ?>
                            <a href="view_course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary">Start Learning</a>
                        <?php else: ?>
                            <a href="membership.php" class="btn">Get Access</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
$mysqli->close();
require_once __DIR__ . '/includes/footer.php';
?>
