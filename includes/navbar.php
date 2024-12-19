<?php

use \model\User;

require_once __DIR__ . "/../model/User.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../middleware/middleware.php";


// Ambil data pengguna dari sesi
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<header class="site-header">
    <h1 class="brand-name">KosYuk!</h1>
    <nav class="main-nav">
        <a href="/index.php" class="nav-link">Beranda</a>
        <a href="/pages/events.php" class="nav-link">Cari Kos</a>
        <a href="/pages/about.php" class="nav-link">Tentang Kami</a>
    </nav>
    <?php if ($user): ?>
        <div class="user-profile">
            <div class="auth-buttons">
                <a href="/pages/logout.php" class="sign-btn">Log Out</a>
            </div>
            <div class="profile-container">
                <a href="/../user/dashboard.php" class="profile-wrapper">
                    <img width="50" height="50"
                        src="<?= isset($user["photo"]) ? $user['photo'] : 'https://cdn.builder.io/api/v1/image/assets/TEMP/e0f8083a2af05b8315cb39f0ee55f7ffcc527bc8dad48c168e7cb295095ffb69?placeholderIfAbsent=true&apiKey=9813aeb455d842cea0d227df786a7f1d'; ?>"
                        alt="User profile" class="profile-image" />
                    <span class="profile-name"><?= htmlspecialchars($user['name']); ?></span>
                </a>
            </div>
            
        </div>
    <?php else: ?>
        <div class="auth-buttons">
            <a href="/pages/register.php" class="sign-btn">Register</a>
            <a href="/pages/login.php" class="login-btn">Log In</a>
        </div>
    <?php endif; ?>
</header>