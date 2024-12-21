<?php
if (!session_id()) session_start();

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../middleware/middleware.php";
isLoggedIn();
isAdmin();

$kos_id = $_GET['id'] ?? 0;

if (!isset($kos_id) or $kos_id <= 0) {
    header('Location: /admin/manageEvnt.php');
    exit();
}

// Fetch event details
$query = "SELECT kos_id, title, description, lokasi, fasilitas, harga, url_wa FROM kosan WHERE kos_id = ?";
$stmt = mysqli_prepare($dbs, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt, "i", $kos_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $kos_id, $title, $description, $lokasi, $fasilitas, $harga, $url_wa);
mysqli_stmt_fetch($stmt);

$kos = [
    'kos_id' => $kos_id,
    'title' => $title,
    'description' => $description,
    'lokasi' => $lokasi,
    'fasilitas' => $fasilitas,
    'harga' => $harga,
    'url_wa' => $url_wa
];

mysqli_stmt_close($stmt);

if (!$kos['kos_id']) {
    header('Location: /admin/manageEvnt.php');
    exit();
}

if (isset($_POST["update"])) {
    $title = $_POST["title"] ?? $kos["title"];
    $description = $_POST["description"] ?? $kos["description"]; 
    $lokasi = $_POST["lokasi"] ?? $kos["lokasi"];
    $fasilitas = $_POST["fasilitas"] ?? $kos["fasilitas"]; 
    $harga = $_POST["harga"] ?? $kos["harga"]; 
    $url_wa = $_POST["url_wa"] ?? $kos["url_wa"];

    try {
        $query = "UPDATE kosan SET title = ?,  description = ?, lokasi = ?, fasilitas = ?, harga = ?, url_wa = ? WHERE kos_id = ?";
        $stmt = mysqli_prepare($dbs, $query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, "sssssis", $title, $description, $lokasi, $fasilitas, $harga, $url_wa, $kos_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Success message or redirect
        echo "<script>
        alert('Event berhasil di update');
        location.replace('/admin/manageEvnt.php');
        </script>";
        exit();
    } catch (Exception $e) {
        // Handle the error
        echo "<script>
        alert('Event gagal di update');
        location.replace('/admin/manageEvnt.php');
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/../includes/meta.php"; ?>
    <title>Edit Event</title>
    <link rel="stylesheet" href="/../../assets/style/styles.css">
    <style>
        .form-section {
            margin-top: 31px;
            width: 655px;
            max-width: 100%;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 6px;
            margin-bottom: 30px;
        }

        .form-label {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            margin-top: 50px;
        }


        .form-input {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            padding: 11px 18px;
            border: 1px solid rgba(115, 76, 16, 1);
            font-size: 12px;
            font-weight: 300;
            width: 100%;
            margin-top: 5px;
        }


        .datetime-container {
            display: flex;
            gap: 30px;
            margin-top: 23px;
        }

        .date-input,
        .time-input {
            flex: 1;
        }


        .form-textarea {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            width: 100%;
            padding: 10px 18px 40px;
            border: 1px solid rgba(115, 76, 16, 1);
            font-size: 12px;
            font-weight: 300;
            resize: vertical;
            margin-bottom: 10px;
            margin-top: 5px;
        }

        .upload-container {
            border-radius: 5px;
            background-color: rgba(249, 250, 251, 1);
            display: flex;
            margin-top: 11px;
            flex-direction: column;
            align-items: center;
            color: rgba(115, 76, 16, 1);
            letter-spacing: 0.3px;
            justify-content: center;
            padding: 26px 80px;
            font: 400 12px/2 Poppins, sans-serif;
            border: 1px dashed rgba(115, 76, 16, 1);
        }

        #thumbnail-preview,
        #gallery-preview {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .file-preview {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #000;
        }

        .preview-icon {
            width: 20px;
            height: 20px;
        }

        .visually-hidden {
            display: none;
        }

        .upload-icon {
            width: 32px;
            height: 32px;
        }


        /* button save & cancel */
        .action-buttons {
            align-self: end;
            display: flex;
            margin-top: 34px;
            align-items: center;
            gap: 14px;
            font-size: 20px;
        }

        .btn-cancel {
            border-radius: 24px;
            color: var(--btn-daftar-masuk, #734c10);
            padding: 15px 25px;
            border: 1px solid rgba(115, 76, 16, 1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #ebdac8;
            border-color: #734c10;
            color: #734c10;
        }

        .btn-save {
            border-radius: 24px;
            background-color: rgba(115, 76, 16, 1);
            color: var(--Foundation-Yellow-Light, #f8f3ed);
            padding: 15px 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-save:hover {
            background-color: #ebdac8;
            border: 1px solid #734c10;
            color: #734c10;
        }


        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        @media (max-width: 991px) {

            .action-buttons {
                margin-right: 3px;
            }

            .btn-cancel,
            .btn-save {
                padding: 0 20px;
            }
        }
    </style>
</head>

<body>
<?php include_once __DIR__ . "/../includes/navbarAdm.php"; ?>

<!-- ======= MAIN DASHBOARD ======== -->
<div class="main-dashboard">
    <div class="dashboard">
        <!-- ===== Header ======= -->
        <header class="dashboard-header">
            <h1 class="page-title-dashboard">Edit Postingan</h1>
            <div class="user-profile-dashboard">
                <img class="profile-icon-dashboard" src="/../../assets/images/profile-admin.png" alt="User profile"/>
                <div class="profile-text-dashboard">Admin</div>
            </div>
        </header>

        <!-- ===== Konten Posts ======= -->
        <div class="dashboard-content">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label for="title" class="form-label">Nama Kos</label>
                        <input type="text" name="title" id="title" class="form-input"
                               value="<?= htmlspecialchars($kos["title"]) ?>" required>
                        <label for="fasilitas" class="form-label">Fasilitas</label>
                        <input type="text" name="fasilitas" id="fasilitas" class="form-input"
                               value="<?= htmlspecialchars($kos["fasilitas"]) ?>" required>
                        <label for="location" class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" class="form-input"
                               value="<?= htmlspecialchars($kos["lokasi"]) ?>" required>
                        <div class="datetime-container">
                            <div class="date-input">
                                <label for="date" class="form-label">Kontak</label>
                                <input type="text" name="url_wa" id="url_wa" class="form-input"
                                       value="<?= htmlspecialchars($kos["url_wa"]) ?>" required>
                            </div>
                            <div class="time-input">
                                <label for="time" class="form-label">Harga</label>
                                <input type="text" name="harga" id="harga" class="form-input"
                                       value="<?= htmlspecialchars($kos["harga"]) ?>" required>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-textarea" rows="10"
                                  required><?= htmlspecialchars($kos["description"]) ?></textarea>
                    </div>
                </div>
                <!-- button save and cancel -->
                <div class="action-buttons">
                    <button type="button" class="btn-cancel"
                            onclick="window.location.href='/admin/manageEvnt.php';">Cancel
                    </button>
                    <button type="submit" name="update" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>

</html>
