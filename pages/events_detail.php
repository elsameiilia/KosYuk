<?php
if (!session_id()) session_start();
include_once __DIR__ . "/../config/database.php";

try{
$username = $_SESSION['user']['username'] ?? '';

// Check if event_id or id is set
$kos_id = isset($_GET['kos_id']) ? $_GET['kos_id'] : (isset($_GET['id']) ? $_GET['id'] : null);

if ($kos_id === null) {
    header('Location: /events.php');
    exit();
}

// Fetch event details
$query = "SELECT title, description, lokasi, fasilitas, harga, banner, url_wa FROM kosan WHERE kos_id = ?";
$stmt = mysqli_prepare($dbs, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt, "i", $kos_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $title,  $description, $lokasi, $fasilitas, $harga, $banner, $url_wa);
mysqli_stmt_fetch($stmt);
$kos = [
    'title' => $title,
    'description' => $description,
    'lokasi' => $lokasi,
    'fasilitas' => $fasilitas,
    'harga' => $harga,
    'banner' => $banner,
    'url_wa' => $url_wa
];
mysqli_stmt_close($stmt);

if (!$kos['title']) {
    header("Location: /events.php");
    exit();
}

// Handle bookmark actions
if (isset($_POST['bookmark_action'])) {
    $action = $_POST['bookmark_action'];

    if ($action == 'add') {
        $bookmark_query = "INSERT INTO bookmark (kos_id, username) VALUES (?, ?)";
        $stmt = $dbs->prepare($bookmark_query);
        $stmt->bind_param('is', $kos_id, $username);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'remove') {
        $bookmark_query = "DELETE FROM bookmark WHERE kos_id = ? AND username = ?";
        $stmt = $dbs->prepare($bookmark_query);
        $stmt->bind_param('is', $kos_id, $username);
        $stmt->execute();
        $stmt->close();
    }
}
}catch(Exception $e){
    echo  "<script> location.replace('login.php'); </script>";
}

// Fetch images related to the kosan
$query_images = "SELECT photo FROM img_kosan WHERE kos_id = ?";
$stmt_images = mysqli_prepare($dbs, $query_images);
if (!$stmt_images) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt_images, "i", $kos_id);
mysqli_stmt_execute($stmt_images);
mysqli_stmt_bind_result($stmt_images, $photo);
$images = [];
while (mysqli_stmt_fetch($stmt_images)) {
    $images[] = ['photo' => $photo];
}
mysqli_stmt_close($stmt_images);

// Check if the destination is bookmarked
$bookmark_check_query = "SELECT 1 FROM bookmark WHERE kos_id = ? AND username = ?";
$stmt_check = mysqli_prepare($dbs, $bookmark_check_query);
if (!$stmt_check) {
    die("Prepare failed: " . mysqli_error($dbs));
}
mysqli_stmt_bind_param($stmt_check, 'is', $kos_id, $username);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_bind_result($stmt_check, $is_bookmarked);
$is_bookmarked = mysqli_stmt_fetch($stmt_check);
mysqli_stmt_close($stmt_check);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Cari Kos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/../../assets/style/styles.css"/>
    <style>
        .responsive .gallery img{
        height: 250px;
        width: 250px;
        object-fit: cover;
        object-position: center;
        }
        .hero-section-eventeach img{
            width: 100%;
            height: 500px;
            object-fit: cover;
            object-position: center;
        }
    </style>
</head>
<body>
<div class="placeseach-wrapper">
    <!-- ======== HEADER ======== -->
    <?php include_once __DIR__ . "/../includes/navbar.php"; ?>

    <!-- ======== HERO EVENT-EACH ======== -->
    <section class="hero-section-eventeach">
        <img src="/../uploads/photos/<?= htmlspecialchars($kos['banner']); ?>"
             alt="Scenic view of <?= htmlspecialchars($kos['title']); ?>" class="hero-image-eventeach"/>
</div>
</section>
<nav class="breadcrumb-eventeach" aria-label="Breadcrumb">
    <div class="breadcrumb-list-eventeach">
        <a href="events.php" class="back-nav-eventeach">Cari Kos</a>
        <span>/</span>
        <span><?= htmlspecialchars($kos['title']); ?></span>
    </div>
</nav>

<!-- ======== EVENT KONTEN ======== -->
<main class="eventeach-content">
    <div class="hero-content-placeseach">
                <h1 class="hero-title-placeseach"><?= htmlspecialchars($kos['title']); ?></h1>

                <!-- ====== share & bookmarks ===== -->
                <div class="social-icons-placeseach">
                    <a target="_blank" href="<?= htmlspecialchars($kos['url_wa']); ?>"><img src="/assets/images/whatsapp.png" alt="Social media link" class="social-icon" /></a>
                    <form method="POST" action="">
                        <input type="hidden" name="bookmark_action" value="<?= $is_bookmarked ? 'remove' : 'add'; ?>">
                        <button type="submit" style="border:0;">
                            <img src="/assets/images/bookmark-dashboard.png"
                                 alt="<?= $is_bookmarked ? 'Remove from favorites' : 'Save to favorites'; ?>"
                                 class="bookmark-icon-eventeach"/>
                        </button>
                    </form>
                </div>
    </div>
    <article>
        <h2 class="eventeach-title"><?= htmlspecialchars($kos['title']); ?></h2>
        <p class="eventeach-description">
            <?= nl2br(htmlspecialchars($kos['description'])); ?>
        </p>
    </article>

    <!-- ======== EVENT INFO ======== -->
    <section class="eventeach-details">
        <div class="info-section-eventeach">
            <h3 class="info-title-eventeach">Informasi Kos</h3>
            <div class="info-grid-eventeach">
                <div class="info-label-eventeach">Lokasi</div>
                <div class="info-value-eventeach"><?= htmlspecialchars($kos['lokasi']); ?></div>
                <div class="info-label-eventeach">Fasilitas</div>
                <div class="info-value-eventeach"><?= htmlspecialchars($kos['fasilitas']); ?></div>
                <div class="info-label-eventeach">Harga</div>
                <div class="info-value-eventeach"><?= htmlspecialchars($kos['harga']); ?></div>
            </div>
        </div>
    </section>

    <section>
        <br><br><h3 class="info-title-eventeach">Galeri Kos</h3>
        <div class="responsive">
            <div class="gallery">
                <?php foreach ($images as $image): ?>
                <a target="_self" href="/../uploads/photos/<?= htmlspecialchars($image['photo']); ?>">
                <img class="foto" src="/../uploads/photos/<?= htmlspecialchars($image['photo']); ?>" alt="Foto Kos">
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<!-- =========== FOOTER =========== -->
<?php include_once __DIR__ . "/../includes/footer.php"; ?>

</body>
</html>
