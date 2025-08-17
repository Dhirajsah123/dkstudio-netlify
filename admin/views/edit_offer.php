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
    <title>Edit Offer - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/dashboard.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Offer</h2>
        <form action="offers.php?action=edit&id=<?php echo $data['offer']->id; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($data['offer']->name); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3" required><?php echo htmlspecialchars($data['offer']->description); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="points" class="form-label">Points</label>
                <input type="number" name="points" id="points" class="form-control" value="<?php echo $data['offer']->points; ?>" required>
            </div>
            <div class="mb-3">
                <label for="url" class="form-label">URL</label>
                <input type="url" name="url" id="url" class="form-control" value="<?php echo htmlspecialchars($data['offer']->url); ?>" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" <?php echo $data['offer']->is_active ? 'checked' : ''; ?>>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Update Offer</button>
        </form>
    </div>
</body>
</html>
