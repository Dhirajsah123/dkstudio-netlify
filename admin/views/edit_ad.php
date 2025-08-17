<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
require_once __DIR__ . '/../../php/core/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ad - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/dashboard.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Ad</h2>
        <form action="ads.php?action=edit&id=<?php echo $data['ad']->id; ?>" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($data['ad']->title); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3" required><?php echo htmlspecialchars($data['ad']->description); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="points" class="form-label">Points</label>
                <input type="number" name="points" id="points" class="form-control" value="<?php echo $data['ad']->points; ?>" required>
            </div>
            <div class="mb-3">
                <label for="url" class="form-label">URL</label>
                <input type="url" name="url" id="url" class="form-control" value="<?php echo htmlspecialchars($data['ad']->url); ?>" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" <?php echo $data['ad']->is_active ? 'checked' : ''; ?>>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Update Ad</button>
        </form>
    </div>
</body>
</html>
