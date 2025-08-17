<?php
// session_start(); // Session is already started in the controller
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
    <title>Edit User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/dashboard.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom"><i
                    class="fas fa-user-shield me-2"></i>Admin Panel</div>
            <div class="list-group list-group-flush my-3">
                <a href="dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="users.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
                        class="fas fa-users me-2"></i>Users</a>
                <!-- Other links -->
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Edit User</h2>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <div class="row my-5">
                    <div class="col">
                        <form action="users.php?action=edit&id=<?php echo $data['user']->id; ?>" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($data['user']->username); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($data['user']->email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="balance" class="form-label">Balance</label>
                                <input type="number" step="0.01" name="balance" id="balance" class="form-control" value="<?php echo $data['user']->balance; ?>" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="email_verified" id="email_verified" class="form-check-input" <?php echo $data['user']->email_verified ? 'checked' : ''; ?>>
                                <label for="email_verified" class="form-check-label">Email Verified</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
