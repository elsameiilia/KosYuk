<?php
// php -S localhost:3000
if (!session_id()) session_start();
include_once __DIR__ . "/config/database.php";
include_once __DIR__ . "/middleware/middleware.php";


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
    <link rel="stylesheet" type="text/css" href="assets/style/styles.css"/>
    <title>KosYuk!</title>
</head>

<body>
<div class="landing-container">
    <?php include_once __DIR__ . "/includes/navbar.php"; ?>

    <section class="hero">
        <div class="hero-content">
            <h2>Kos Yuk!<br></h2>
            <h1>Temukan Kos<br>Impianmu</h1>
            <a href="#main-content"><button class="jelajahi-btn">Cari Sekarang!</button></a>
        </div>
    </section>

    <section class="about curved-top">
        <div class="about-img">
            <img src="assets/images/abouthomepage.jpeg" alt="Purwokerto">
        </div>
        <div class="about-content">
            <h2>Apa itu si Kos Yuk!</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed magnam rerum nisi, autem rem in officiis illum veritatis accusantium voluptates laborum, mollitia cum harum totam. Ratione adipisci enim aut temporibus.</p>
            <a href="pages/about.php"><button class="jelajahi-btn">Selengkapnya</button></a>
        </div>
    </section>

    <main class="main-content" id="main-content">
        <!-- ========= EVENT ========= -->
        <section class="events-section">
            <div class="events-content">
                <h2 class="events-heading">REKOMENDASI</h2>
                <p class="events-description">Beberapa kosan yang mungkin anda suka!</p>
                <a href="pages/events.php" class="cta-button-discover"><b>Lebih Banyak</b></a>
            </div>

            <div class="events-gallery">
                <?php foreach ($kosan as $kos): ?>
                    <article class="event-card-home" tabindex="0">
                        <img loading="lazy" src="/uploads/photos/<?= $kos['banner']; ?>"
                             alt="image of <?= $kos['title']; ?>" class="event-image-home">
                        <a href="/pages/events_detail.php?id=<?= $kos['kos_id'] ?>" class="event-details-home" onclick="return checkLogin(event);"> 
                            <div class="event-info-home">
                                <h2 class="event-title-home"><br><?= $kos['title']; ?></h2>
                                <h2 class="event-date-home"><br><?= $kos['harga']; ?></h2>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>

            </div>
        </section>
    </main>

    <?php include_once __DIR__ . "/includes/footer.php"; ?>
</div>

<script> 
    function checkLogin(event) { 
        <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?> 
        event.preventDefault(); // Mencegah link diakses 
        alert('Anda harus login untuk mengakses detail event.'); 
        window.location.href = 'pages/login.php'; 
        return false; 
        <?php endif; ?> 
        return true; } 
        </script>
</body>

</html>
