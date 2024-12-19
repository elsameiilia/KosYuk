<?php
// php -S localhost:3000
if (!session_id()) session_start();
include_once __DIR__ . "/config/database.php";


// Query untuk events
$query = "SELECT kos_id, title, harga, banner FROM kosan ORDER BY kos_id DESC LIMIT 3";
$stmt = $dbs->prepare($query);
$stmt->execute();
$stmt->bind_result($kos_id, $title, $harga, $banner);

$kosan = [];
while ($stmt->fetch()) {
    $kosan[] = [
        'kos_id' => $kos_id,
        'title' => $title,
        'harga' => $harga,
        'banner' => $banner,
    ];
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once __DIR__ . "/includes/meta.php"; ?>
    <link rel="stylesheet" type="text/css" href="/../../assets/style/styles.css"/>
    <title>KosYuk!</title>
</head>

<body>
<div class="landing-container">
    <?php include_once __DIR__ . "/includes/navbar.php"; ?>

    <!-- ======== BERANDA ======== -->
    <section class="hero-section">
        <img src="/images/assets/landing-page.png" alt="Egyptian landscape panorama" class="hero-img"/>
        <div class="hero-content">
            <p class="hero-subtitle">DISCOVER EGYPT</p>
            <h2 class="hero-title">Experience Unforgettable Landscape, Relive History!</h2>
            <a href="#main-content" class="cta-button-explore">Explore more</a>
        </div>
    </section>


    <main class="main-content" id="main-content">

        <!-- ========= EVENT ========= -->
        <section class="events-section">
            <div class="events-content">
                <h2 class="events-heading">WHAT'S ON</h2>
                <p class="events-description">Dive into rich history, stunning landscapes, and cultural experiences that
                    will leave you inspired.</p>
                <a href="/pages/events.php" class="cta-button-discover"><b>Discover more events</b></a>
            </div>

            <div class="events-gallery">
                <?php foreach ($kosan as $kos): ?>
                    <article class="event-card-home" tabindex="0">
                        <img loading="lazy" src="/../uploads/photos/<?= $kos['banner']; ?>"
                             alt="image of <?= $kos['title']; ?>" class="event-image-home">
                        <a href="/pages/events_detail.php?id=<?= $kos['kos_id'] ?>" class="event-details-home">
                            <div class="event-info-home">
                                <h2 class="event-title-home"><br><?= $kos['title']; ?></h2>
                                <h2 class="event-date-home"><br><?= $kos['harga']; ?></h2>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>

            </div>
        </section>
        <!-- ========== GALLERY ========== 
        <section class="gallery-section">
            <h2 class="gallery-heading">GALLERY</h2>
            <p class="gallery-subtitle">A glimpse of heaven on earth were found in Egypt.</p>
            <div class="gallery-grid">
                <img src="/images/assets/gallery1.png" alt="Egyptian landscape" class="gallery-main gallery-img"/>
                <div class="gallery-side">
                    <img src="/images/assets/gallery2.png" alt="Historical site" class="gallery-img"/>
                    <div class="gallery-row">
                        <img src="/images/assets/gallery3.png" alt="Cultural scene" class="gallery-img"/>
                        <img src="/images/assets/gallery4.png" alt="Traditional architecture" class="gallery-img"/>
                    </div>
                </div>
            </div>
        </section>-->
    </main>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>
</div>
</body>

</html>
