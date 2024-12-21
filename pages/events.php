<?php
// php -S localhost:3000
if (!session_id()) session_start();
include_once(__DIR__ . '/../config/database.php');

// Setting up pagination variables
$limit = 16;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$off = ($page * $limit) - $limit;

// Handling search input
$search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$search_harga = isset($_GET['search_harga']) ? $_GET['search_harga'] : '';

// Constructing the base query
$query_base = "SELECT kos_id, title, harga, banner FROM kosan WHERE 1=1";

// Adding conditions for search
if ($search_name) {
    $query_base .= " AND title LIKE ?";
}

// Preparing the count query
$total_query = "SELECT COUNT(*) as total FROM ($query_base) as count_query";
$stmt = mysqli_prepare($dbs, $total_query);
if (!$stmt) {
    die("Error in count query: " . mysqli_error($dbs));
}
if ($search_name) {
    mysqli_stmt_bind_param($stmt, 's', $search_name);
} 

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$total_pages = ceil($total / $limit); // Calculate total pages

// Preparing the main query with pagination
if ($search_name) {
    // Query with search parameter
    $query = $query_base . " ORDER BY kos_id DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($dbs, $query);
    if (!$stmt) {
        die("Error in query with search: " . mysqli_error($dbs));
    }
    $search_param = '%' . $search_name . '%'; // Add wildcards for LIKE clause
    mysqli_stmt_bind_param($stmt, 'sii', $search_param, $limit, $off);
} else {
    // Query without search parameter
    $query = $query_base . " ORDER BY kos_id DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($dbs, $query);
    if (!$stmt) {
        die("Error in main query: " . mysqli_error($dbs));
    }
    mysqli_stmt_bind_param($stmt, 'ii', $limit, $off);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $kos_id, $title, $harga, $banner);

$kosan = [];
while (mysqli_stmt_fetch($stmt)) {
    $kosan[] = [
        'kos_id' => $kos_id,
        'title' => $title,
        'harga' => $harga,
        'banner' => $banner,
    ];
}

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8"/>
    <title>Cari Kos</title>
    <link rel="stylesheet" type="text/css" href="/../../assets/style/styles.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<!-- ======== HEADER ======== -->
<div class="landing-container">

    <?php include_once (__DIR__ . "/../includes/navbar.php"); ?>

    <!-- ======== HERO SECTION ======== -->
    <section class="hero-section-eventlist">
        <img src="../../assets/images/hero-posts.jpeg" class="hero-image-eventlist"/>
        <h1 class="hero-title-eventlist">Cari Kos</h1>
    </section>

    <div class="latar">
        <div class="eventlist-section">
            <!-- ======== SEARCH BAR ======== -->
            <div class="search-container">
                <form method="GET" action="" id="search-form">
                    <div class="filter-item"><label for="name">Nama Kosan</label> <input type="text" name="search_name" placeholder="Search by name" value="<?= htmlspecialchars($search_name); ?>">
                    </div>
                    <button class="search-button" type="submit" form="search-form">Search</button>
                </form>
                
            </div>
            <div class="upcoming-container">
                <h2 class="upcoming-event-title">Cari Kos yang Kayak Apa?</h2><br>
            </div>
        </div>
        <div class="eventlist-grid">
            <?php foreach ($kosan as $kos): ?>
                <article class="event-card" tabindex="0">
                    <img loading="lazy" src="/../uploads/photos/<?= $kos["banner"] ?>" class="eventlist-image" alt="<?= $kos['title'] ?>"/>
                    <a href="/pages/events_detail.php?id=<?= $kos['kos_id'] ?>" class="event-details" onclick="return checkLogin(event);">
                        <div class="event-info">
                            <h2 class="event-title"><?= $kos['title'] ?></h2>
                            <h2 class="event-title">Rp.<?= $kos['harga'] ?>/bulan</h2>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    

    <!-- Pagination links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1; ?>&search_name=<?= htmlspecialchars($search_name); ?>&search_date=<?= htmlspecialchars($search_date); ?>">&laquo;
                    Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i; ?>&search_name=<?= htmlspecialchars($search_name); ?>&search_date=<?= htmlspecialchars($search_date); ?>"
                    <?php if ($i == $page)
                        echo 'class="active"'; ?>><?= $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a
                        href="?page=<?= $page + 1; ?>&search_name=<?= htmlspecialchars($search_name); ?>&search_date=<?= htmlspecialchars($search_date); ?>">Next
                    &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<script> 
    function checkLogin(post) { 
        <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?> 
        post.preventDefault(); // Mencegah link diakses 
        alert('Anda harus login untuk mengakses detail post.'); 
        window.location.href = '../pages/login.php'; 
        return false; 
        <?php endif; ?> 
        return true; } 
</script>
    <!-- =========== FOOTER =========== -->
    <?php include_once (__DIR__ . "/../includes/footer.php"); ?>

</body>

</html>