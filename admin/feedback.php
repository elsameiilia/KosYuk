<?php
if (!session_id()) session_start();

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

// Fetch feedback details
$query = "SELECT fb_id, nama, email, pesan FROM feedback";
$result = mysqli_query($dbs, $query);
$feedbacks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $feedbacks[] = $row;
}
mysqli_free_result($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../includes/meta.php"; ?>
    <title>Feedback</title>
    <link rel="stylesheet" href="/../../assets/style/styles.css">
</head>

<body>
<div class="container-dashboard">

    <?php include_once __DIR__ . "/../includes/navbarAdm.php"; ?>

    <!-- ======= MAIN DASHBOARD ========  -->
    <div class="main-dashboard">
        <div class="dashboard">
            <!-- ======= MAIN DASHBOARD ========  -->
            <div class="main-dashboard">
                <div class="dashboard">

                    <!-- ===== Header =======  -->
                    <header class="dashboard-header">
                        <h1 class="page-title-dashboard">Feedback</h1>
                        <div class="user-profile-dashboard">
                            <img class="profile-icon-dashboard" src="../assets/images/profile-admin.png"
                                 alt="User profile"/>
                            <div class="profile-text-dashboard">Admin</div>
                        </div>
                    </header>

                    <!-- ===== Konten Stats ======= -->
                    <section class="stats-grid" aria-label="Dashboard Statistics">
                        <?php foreach ($feedbacks as $feedback): ?>
                            <div class="feedback-container">
                                <div class="feedback">
                                    <h2>Sender: <?= htmlspecialchars($feedback['nama']) ?></h2>
                                    <div><?= htmlspecialchars($feedback['email']) ?></div>
                                    <p><?= nl2br(htmlspecialchars($feedback['pesan'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>
