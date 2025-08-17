<?php
// session_start(); // Session is already started in the controller
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../core/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/dashboard.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom"><i
                    class="fas fa-money-bill-wave me-2"></i>CashMitra</div>
            <div class="list-group list-group-flush my-3">
                <a href="dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="offerwall.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-wallet me-2"></i>Offerwall</a>
                <a href="ads.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-ad me-2"></i>Ads</a>
                <a href="referral.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i
                        class="fas fa-user-friends me-2"></i>Referral</a>
                <a href="withdraw.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-hand-holding-usd me-2"></i>Withdraw</a>
                <a href="profile.php" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                        class="fas fa-user me-2"></i>Profile</a>
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
                    <h2 class="fs-2 m-0">Referral</h2>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle second-text fw-bold" href="#" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i><?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <div class="row my-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3>Your Referral Link</h3>
                            </div>
                            <div class="card-body">
                                <p>Share this link with your friends to earn referral commissions.</p>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="referral-link" value="<?php echo BASE_URL . '/register.php?ref=' . $data['user']->referral_code; ?>" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="copy-button">Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row my-5">
                    <h3 class="fs-4 mb-3">Your Referrals</h3>
                    <div class="col">
                        <table class="table bg-white rounded shadow-sm  table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Username</th>
                                    <th scope="col">Date Joined</th>
                                    <th scope="col">Commission Earned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['referred_users'])) : ?>
                                    <?php foreach ($data['referred_users'] as $referred_user) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($referred_user->username); ?></td>
                                            <td><?php echo date('d M Y', strtotime($referred_user->created_at)); ?></td>
                                            <td>$<?php echo number_format($referred_user->commission_earned, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="3" class="text-center">You haven't referred anyone yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('copy-button').addEventListener('click', function() {
            var copyText = document.getElementById('referral-link');
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            this.textContent = 'Copied!';
            setTimeout(() => {
                this.textContent = 'Copy';
            }, 2000);
        });

        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>
</body>
</html>
