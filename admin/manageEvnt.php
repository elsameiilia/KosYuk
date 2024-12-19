<?php

if (!session_id()) session_start();

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

// Fetch events
$query = "SELECT kos_id, title, banner FROM kosan GROUP BY kos_id ORDER BY kos_id ASC";
$stmt = mysqli_prepare($dbs, $query);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $kos_id, $title, $banner);

$kosan = [];
while (mysqli_stmt_fetch($stmt)) {
    $kosan[] = [
        'kos_id' => $kos_id,
        'title' => $title,
        'banner' => $banner
    ];
}
mysqli_stmt_close($stmt);

if (isset($_POST['delete'])) {
    // Fetch event details for deletion
    $stmt = mysqli_prepare($dbs, 'SELECT kos_id, banner FROM kosan WHERE kos_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $_POST['kos_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $kos_id, $banner);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($kos_id) {
        $pathdir = __DIR__ . '/../uploads/photos/';

        if (file_exists($pathdir . $banner)) {
            unlink($pathdir . $banner);
        }

        // Delete the event from database
        $stmt = mysqli_prepare($dbs, "DELETE FROM kosan WHERE kos_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $kos_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<script>
            alert('Delete successfully');
            location.replace('/admin/manageEvnt.php');
        </script>";
    } else {
        echo "<script>
            alert('ERROR: Events not found');
            location.replace('/admin/manageEvnt.php');
        </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../includes/meta.php"; ?>
    <title>Kelola Postingan</title>
    <link rel="stylesheet" href="/../../assets/style/styles.css">
    <style>
        .places-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: flex-start;
            margin: 20px auto;
        }


        .place-card {
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
            background: #fff;
            max-width: 200px;
            width: 100%;
            overflow: hidden;
        }


        .image-container {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .place-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }


        .action-buttons {
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            gap: 8px;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }

        .card-content {
            padding: 12px;
            text-align: left;
            background-color: #734c10;
            color: #eef1f6;
            font-family: "Sora", sans-serif;
            font-size: 14px;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .place-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .place-date {
            font-weight: 300;
            font-size: 12px;
            opacity: 0.8;
        }


        .add-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #734c10;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            padding: 10px 16px;
            margin: 20px;
            border-radius: 8px;
            cursor: pointer;
            z-index: 100;
            border: none;
        }

        .add-button:hover {
            background: #5e3a0e;
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            .places-grid {
                gap: 12px;
            }

            .place-card {
                max-width: 150px;
            }

            .card-content {
                font-size: 12px;
            }

            .add-button {
                padding: 8px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

<div class="container-dashboard">

    <?php include_once __DIR__ . "/../includes/navbarAdm.php"; ?>

    </div> <!-- ======= MAIN DASHBOARD ======== -->
    <div class="main-dashboard">
        <div class="dashboard"> <!-- ===== Header ======= -->
            <header class="dashboard-header">
                <h1 class="page-title-dashboard">Kelola Postingan</h1>
                <div class="user-profile-dashboard"><img class="profile-icon-dashboard"
                                                         src="../images/assets/profile-admin.png" alt="User profile"/>
                    <div class="profile-text-dashboard">Admin</div>
                </div>
            </header> <!-- ===== Konten ======= -->
            <div class="dashboard-content">
                <div class="places-grid">
                    <?php foreach ($kosan as $data): ?>
                        <article class="place-card">
                            <div class="image-container"><img src="/../uploads/photos/<?= $data["banner"]; ?>"
                                                              alt="Event banner" class="place-image"/>
                                <div class="action-buttons">
                                    <!-- Edit Button -->
                                    <button class="action-icon"
                                            aria-label="Edit event"
                                            onclick="window.location.href='/admin/editEvnt.php?id=<?= htmlspecialchars($data["kos_id"]); ?>';">
                                        ✏️
                                    </button>
                                    <!-- Delete Button -->
                                    <form action="" method="post">
                                        <input type="hidden" name="kos_id" value="<?= $data["kos_id"]; ?>">
                                        <button class="action-icon" type="submit" name="delete"
                                                aria-label="Delete event">❌
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="content-wrapper">
                                    <h2 class="place-title"><?= $data["title"]; ?></h2>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div> <!-- Add Post Button -->
                <button class="add-button"
                        onclick="window.location.href='/admin/createEvnt.php';"> Add Post
                </button>
            </div>
        </div>
    </div>
</div>
</body>

</html>