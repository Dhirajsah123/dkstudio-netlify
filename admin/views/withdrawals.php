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
    <title>Manage Withdrawals - Admin</title>
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
                <a href="users.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-users me-2"></i>Users</a>
                <a href="offers.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-wallet me-2"></i>Offers</a>
                <a href="ads.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-ad me-2"></i>Ads</a>
                <a href="withdrawals.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
                        class="fas fa-hand-holding-usd me-2"></i>Withdrawals</a>
                <a href="settings.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-cog me-2"></i>Settings</a>
                <a href="logout.php" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i
                        class="fas fa-power-off me-2"></i>Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Manage Withdrawals</h2>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <div class="row my-5">
                    <div class="col">
                        <table class="table bg-white rounded shadow-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Details</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['withdrawals'] as $withdrawal) : ?>
                                    <tr>
                                        <td><?php echo $withdrawal->id; ?></td>
                                        <td><?php echo htmlspecialchars($withdrawal->username); ?></td>
                                        <td><?php echo $withdrawal->amount; ?></td>
                                        <td><?php echo htmlspecialchars($withdrawal->method); ?></td>
                                        <td><?php echo htmlspecialchars($withdrawal->details); ?></td>
                                        <td><?php echo $withdrawal->created_at; ?></td>
                                        <td>
                                            <span class="badge
                                                <?php if ($withdrawal->status == 'approved') echo 'bg-success';
                                                elseif ($withdrawal->status == 'rejected') echo 'bg-danger';
                                                else echo 'bg-warning'; ?>">
                                                <?php echo ucfirst($withdrawal->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($withdrawal->status == 'pending') : ?>
                                                <a href="withdrawals.php?action=approve&id=<?php echo $withdrawal->id; ?>" class="btn btn-sm btn-success">Approve</a>
                                                <a href="withdrawals.php?action=reject&id=<?php echo $withdrawal->id; ?>" class="btn btn-sm btn-danger">Reject</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
