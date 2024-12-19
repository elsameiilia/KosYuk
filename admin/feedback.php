<?php
if (!session_id()) session_start();

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

// Fetch feedback details
$query = "SELECT nama, email, pesan FROM feedback WHERE fb_id = ?";
$stmt = mysqli_prepare($dbs, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt, "i", $fb_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nama, $email, $pesan);
mysqli_stmt_fetch($stmt);
$feedback = [
    'nama' => $nama,
    'email' => $email,
    'pesan' => $pesan,
];
mysqli_stmt_close($stmt_users);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../includes/meta.php"; ?>
    <title>Dashboard Admin</title>
    <style>
        .stats-grid {
            display: flex;
            margin-top: 31px;
            align-items: center;
            gap: 36px;
            font-family: Poppins, sans-serif;
            justify-content: start;
            flex-wrap: wrap;
        }

        .stats-card {
            border-radius: 0;
            align-self: stretch;
            display: flex;
            min-width: 240px;
            flex-direction: column;
            width: 327px;
            margin: auto 0;
        }

        .card-content {
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            width: 100%;
            flex-direction: column;
            align-items: start;
            justify-content: center;
            padding: 31px;
        }

        @media (max-width: 991px) {
            .card-content {
                padding: 0 20px;
            }
        }

        .stats-wrapper {
            display: flex;
            align-items: end;
            gap: 23px;
            justify-content: start;
        }

        .stats-icon {
            aspect-ratio: 1;
            object-fit: contain;
            object-position: center;
            width: 73px;
        }

        .stats-info {
            display: flex;
            flex-direction: column;
            justify-content: start;
        }

        .stats-number {
            color: black;
            font-size: 30px;
            font-family: 'Sora', sans-serif;
            font-weight: 600;
        }

        .stats-label {
            color: rgb(116, 113, 113);
            font-size: 20px;
            margin-top: 17px;
        }
    </style>
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
                        <h1 class="page-title-dashboard">Dashboard</h1>
                        <div class="user-profile-dashboard">
                            <img class="profile-icon-dashboard" src="../images/assets/profile-admin.png"
                                 alt="User profile"/>
                            <div class="profile-text-dashboard">Admin</div>
                        </div>
                    </header>

                    <!-- ===== Konten Stats ======= -->
                        <section class="stats-grid" aria-label="Dashboard Statistics">
                            <div class="stats-card">
                                <div class="card-content">
                                    <div class="stats-wrapper"><img class="stats-icon"
                                                                    src="../images/assets/stats-icon1.png"
                                                                    alt="Users icon"/>
                                        <div class="stats-info">
                                            <div class="stats-number"><?= htmlspecialchars($total_users); ?></div>
                                            <div class="stats-label">Total Users</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="manageEvnt.php" class="stats-card">
                                <div class="card-content">
                                    <div class="stats-wrapper"><img class="stats-icon"
                                                                    src="../images/assets/stats-icon2.png"
                                                                    alt="Posts icon"/>
                                        <div class="stats-info">
                                            <div class="stats-number"><?= htmlspecialchars($total_postings); ?>
                                            </div>
                                            <div class="stats-label">Total Posts</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>