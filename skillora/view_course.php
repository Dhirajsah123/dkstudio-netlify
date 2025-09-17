<?php
// skillora/view_course.php
require_once __DIR__ . '/includes/header.php';

// 1. Authentication & Authorization
if (!isset($_SESSION['user_id'])) {
    redirect('login.php?status=error&msg=' . urlencode('You must be logged in to view courses.'));
}

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($course_id <= 0) {
    redirect('courses.php?status=error&msg=' . urlencode('Invalid course specified.'));
}

// Check if the user has access to this course via the pivot table.
$user_id = $_SESSION['user_id'];
$has_access = false;
$stmt = $mysqli->prepare(
    "SELECT cm.course_id FROM course_memberships cm
     JOIN users u ON cm.membership_id = u.membership_id
     WHERE u.id = ? AND cm.course_id = ?"
);
$stmt->bind_param('ii', $user_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $has_access = true;
}
$stmt->close();

if (!$has_access) {
    redirect('courses.php?status=error&msg=' . urlencode('You do not have access to this course. Please upgrade your membership.'));
}

// 2. Fetch Course and Lesson Data
$course_stmt = $mysqli->prepare("SELECT * FROM courses WHERE id = ?");
$course_stmt->bind_param('i', $course_id);
$course_stmt->execute();
$course = $course_stmt->get_result()->fetch_assoc();
$course_stmt->close();

if (!$course) {
    redirect('courses.php?status=error&msg=' . urlencode('Course not found.'));
}

$lessons_stmt = $mysqli->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY lesson_order ASC");
$lessons_stmt->bind_param('i', $course_id);
$lessons_stmt->execute();
$lessons = $lessons_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$lessons_stmt->close();

$page_title = $course['title'];
?>

<section class="course-view-section">
    <div class="container">
        <h2><?php echo sanitize_output($course['title']); ?></h2>
        <p class="course-provider">Provided by: <?php echo sanitize_output($course['provider']); ?></p>
        <hr>

        <div class="course-layout">
            <div class="video-player-area">
                <div class="video-wrapper">
                    <iframe id="lesson-video-player" src="<?php echo !empty($lessons) ? sanitize_output($lessons[0]['iframe_url']) : ''; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <h3 id="lesson-title"><?php echo !empty($lessons) ? sanitize_output($lessons[0]['title']) : 'No lessons available for this course.'; ?></h3>
            </div>
            <div class="lesson-playlist-area">
                <h4>Course Syllabus</h4>
                <ul class="lesson-list">
                    <?php if (empty($lessons)): ?>
                        <li>No lessons found.</li>
                    <?php else: ?>
                        <?php foreach ($lessons as $index => $lesson): ?>
                            <li class="<?php echo ($index == 0) ? 'active' : ''; ?>">
                                <a href="#" class="lesson-link" data-video-url="<?php echo sanitize_output($lesson['iframe_url']); ?>" data-lesson-title="<?php echo sanitize_output($lesson['title']); ?>">
                                    <?php echo sanitize_output($lesson['lesson_order']) . '. ' . sanitize_output($lesson['title']); ?>
                                    <span>(<?php echo sanitize_output($lesson['duration']); ?> mins)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="course-description-area">
            <h4>About this course</h4>
            <p><?php echo nl2br(sanitize_output($course['description'])); ?></p>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
