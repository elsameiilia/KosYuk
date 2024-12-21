<?php
if (!session_id()) session_start();

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

// Set up pagination
$limit = 4;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$off = ($page * $limit) - $limit;

// Query to count total feedback
$total_query = "SELECT COUNT(*) as total FROM feedback";
$result_total = mysqli_query($dbs, $total_query);
$total_row = mysqli_fetch_assoc($result_total);
$total_feedback = $total_row['total'];
$total_pages = ceil($total_feedback / $limit);
mysqli_free_result($result_total);

// Fetch feedback details with pagination
$query = "SELECT fb_id, nama, email, pesan FROM feedback LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($dbs, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt, "ii", $limit, $off);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$feedbacks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $feedbacks[] = $row;
}
mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../includes/meta.php"; ?>
    <title>Feedback</title>
    <link rel="stylesheet" href="/../../assets/style/styles.css">
    <style>
        .feedback {
            background-color: #F5F5DC;
            border-style: solid;
            border-radius: 7px;
            border-width: 2px;
            margin: 20px;
            padding: 15px;
            height: 100px;
            width: 980px;
        }

        .pesan {
            font-size: 20px;
        }

        .email {
            font-size: 20px;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            color: #000;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #ddd;
        }
    </style>
</head>

<body>
<div class="container-dashboard">
    <?php include_once __DIR__ . "/../includes/navbarAdm.php"; ?>

    <!-- ======= MAIN DASHBOARD ========  -->
    <div class="main-dashboard">
        <div class="dashboard">
            <!-- ===== Header =======  -->
            <header class="dashboard-header">
                <h1 class="page-title-dashboard">Feedback</h1>
                <div class="user-profile-dashboard">
                    <img class="profile-icon-dashboard" src="../assets/images/profile-admin.png" alt="User profile"/>
                    <div class="profile-text-dashboard">Admin</div>
                </div>
            </header>

            <!-- ===== Konten Stats ======= -->
            <section class="stats-grid" aria-label="Dashboard Statistics">
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="feedback-admin-container">
                        <div class="feedback">
                            <h2 class="sender">Sender: <?= htmlspecialchars($feedback['nama']) ?></h2>
                            <div class="email"><?= htmlspecialchars($feedback['email']) ?></div>
                            <p class="pesan">Pesan: <?= nl2br(htmlspecialchars($feedback['pesan'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- ===== Pagination ======= -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1; ?>">&laquo; Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i; ?>" <?= $i == $page ? 'class="active"' : ''; ?>><?= $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>

</html>