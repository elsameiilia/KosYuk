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
    $title = $_POST["title"]?? "";
    $description = $_POST["description"]?? "";
    $lokasi = $_POST["lokasi"]?? "";
    $fasilitas = $_POST["fasilitas"]?? "";
    $harga = $_POST["harga"]?? 0.0;
    $url_wa = $_POST["url_wa"]?? "";

    try {
        $query = "UPDATE kosan SET title = ?,  description = ?, lokasi = ?, fasilitas = ?, harga = ?, url_wa = ? WHERE kos_id = ?";
        $stmt = mysqli_prepare($dbs, $query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($dbs));
        }
        mysqli_stmt_bind_param($stmt, "ssssdis", $title, $description, $lokasi, $fasilitas, $harga, $url_wa, $kos_id);
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
</head>

<body>
<?php include_once __DIR__ . "/../includes/navbarAdm.php"; ?>

<!-- ======= MAIN DASHBOARD ======== -->
<div class="main-dashboard">
    <div class="dashboard">
        <!-- ===== Header ======= -->
        <header class="dashboard-header">
            <h1 class="page-title-dashboard">Edit Event</h1>
            <div class="user-profile-dashboard">
                <img class="profile-icon-dashboard" src="../assets/profile-admin.png" alt="User profile"/>
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
                        <input type="text" name="location" id="location" class="form-input"
                               value="<?= htmlspecialchars($kos["lokasi"]) ?>" required>
                        <div class="datetime-container">
                            <div class="date-input">
                                <label for="date" class="form-label">Kontak</label>
                                <input type="text" name="url_wa" id="url_wa" class="form-input"
                                       value="<?= htmlspecialchars($kos["url_wa"]) ?>" required>
                            </div>
                            <div class="time-input">
                                <label for="time" class="form-label">Harga</label>
                                <input type="number" name="harga" id="harga" class="form-input"
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