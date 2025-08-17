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
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/dashboard.min.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4">CashMitra</div>
            <div class="list-group list-group-flush my-3">
                <a href="dashboard.php" class="list-group-item list-group-item-action active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="offerwall.php" class="list-group-item list-group-item-action"><i class="fas fa-wallet"></i> Offerwalls</a>
                <a href="ads.php" class="list-group-item list-group-item-action"><i class="fas fa-ad"></i> Ads</a>
                <a href="referral.php" class="list-group-item list-group-item-action"><i class="fas fa-user-friends"></i> Referrals</a>
                <a href="withdraw.php" class="list-group-item list-group-item-action"><i class="fas fa-hand-holding-usd"></i> Withdraw</a>
                <a href="profile.php" class="list-group-item list-group-item-action"><i class="fas fa-user"></i> Profile</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger"><i class="fas fa-power-off"></i> Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left text-primary fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-4 m-0">👋 Welcome back, <?php echo $_SESSION['user_name']; ?></h2>
                </div>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <div class="p-2 bg-white rounded-pill shadow-sm">
                                <span><i class="fas fa-coins text-warning"></i> <?php echo number_format($data['user']->balance, 0); ?></span> |
                                <span>₹ <?php echo number_format($data['user']->balance / 100, 2); ?></span>
                            </div>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-bell"></i></a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <!-- Balance Cards -->
                <div class="row g-3 my-2">
                    <div class="col-md-4">
                        <div class="card p-3">
                            <div class="card-body">
                                <h5 class="card-title">Main Balance</h5>
                                <h2><?php echo number_format($data['user']->balance, 0); ?> Coins</h2>
                                <p>₹ <?php echo number_format($data['user']->balance / 100, 2); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                         <div class="card p-3">
                            <div class="card-body">
                                <h5 class="card-title">Offerwall Balance</h5>
                                <h2>0 Coins</h2>
                                <p>₹ 0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="withdraw.php" class="glowing-button text-center d-block text-decoration-none py-4">Withdraw</a>
                    </div>
                </div>

                <!-- Offerwalls Section -->
                <div class="row my-5">
                    <h3 class="fs-4 mb-3">Offerwalls</h3>
                    <?php if (!empty($data['offerwall_providers'])) : ?>
                        <?php foreach ($data['offerwall_providers'] as $provider) : ?>
                            <div class="col-md-3 mb-3">
                                <div class="card text-center p-3 h-100">
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($provider->logo_url); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($provider->name); ?>" style="max-height: 100px; object-fit: contain;" loading="lazy">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?php echo htmlspecialchars($provider->name); ?></h5>
                                        <p class="card-text"><i class="fas fa-star text-warning"></i> <?php echo $provider->rating; ?></p>
                                        <a href="#" class="btn btn-primary mt-auto">Start Now</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="col">
                            <p>No offerwalls available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ads Section -->
                <div class="row my-5">
                    <h3 class="fs-4 mb-3">Ads</h3>
                    <?php if (!empty($data['ads'])) : ?>
                        <?php foreach (array_slice($data['ads'], 0, 4) as $ad) : ?>
                            <div class="col-md-3 mb-3">
                                <div class="card text-center p-3 h-100">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?php echo htmlspecialchars($ad->title); ?></h5>
                                        <p class="card-text">Points: <?php echo $ad->points; ?></p>
                                        <a href="ads.php" class="btn btn-primary mt-auto">Watch Now</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="col">
                            <p>No ads available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Referrals Section -->
                <div class="row my-5">
                    <h3 class="fs-4 mb-3">Referrals</h3>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <p><strong>Total Referrals:</strong> <?php echo $data['referral_summary']['count'] ?? 0; ?></p>
                            <p><strong>Referral Earnings:</strong> $<?php echo number_format($data['referral_summary']['earnings'] ?? 0, 2); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <p><strong>Your Referral Link:</strong></p>
                            <input type="text" class="form-control" value="<?php echo BASE_URL . '/register.php?ref=' . $data['user']->referral_code; ?>" readonly>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="row my-5">
                    <h3 class="fs-4 mb-3">Recent Transactions</h3>
                    <div class="col">
                        <table class="table bg-white rounded shadow-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['recent_transactions'])) : ?>
                                    <?php foreach ($data['recent_transactions'] as $transaction) : ?>
                                        <tr>
                                            <td><?php echo ucfirst($transaction->type); ?></td>
                                            <td><?php echo htmlspecialchars($transaction->description); ?></td>
                                            <td><?php echo $transaction->amount; ?></td>
                                            <td><?php echo date('d M Y', strtotime($transaction->created_at)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No recent transactions.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="row my-3">
                    <div class="col-md-4"><a href="#" class="btn btn-lg btn-success w-100">🎁 Claim Reward</a></div>
                    <div class="col-md-4"><a href="offerwall.php" class="btn btn-lg btn-info w-100">🌐 Go to Offerwall</a></div>
                    <div class="col-md-4"><a href="ads.php" class="btn btn-lg btn-warning w-100">▶ Watch Ads</a></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo BASE_URL; ?>/public/js/adblock.min.js"></script>
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>
</body>
</html>
