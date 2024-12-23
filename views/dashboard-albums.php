<?php
session_start();
include '../config/db_config.php';

// Periksa apakah pengguna telah login
if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Album Musik</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <script src="../public/js/albums.js" defer></script>
</head>

<body>
    <header class="header">
        <div class="container">
            <a href="../index.php" class="logo">TheSongsIsYours</a>
            <p>Platform untuk menyampaikan pesan mendalam melalui musik</p>
        </div>
    </header>

    <nav class="navigation">
        <ul>
            <li><a href="public-albums.php">Jelajahi Pesan</a></li>
            <li><a href="dashboard-albums.php">Dashboard</a></li>
            <li><a href="cookie-local-session.html">Apk Session, Cookie, Local</a></li>
            <li><a href="../controllers/logout.php" class="logout-button">Logout</a></li>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <h1>Selamat Datang di Dashboard Album Musik</h1>
            <p>Kelola album musik yang telah Anda buat dan bagikan pesan mendalam melalui musik</p>

            <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</p>
            <p>IP Address: <strong><?php echo htmlspecialchars($_SESSION['ip_address']); ?></strong></p>
            <p>Browser: <strong><?php echo htmlspecialchars($_SESSION['browser']); ?></strong></p>
            <p>Cookie PHPSESSID: <strong>
                    <?php
                    echo isset($_COOKIE['PHPSESSID']) ? htmlspecialchars($_COOKIE['PHPSESSID']) : 'Tidak ada cookie PHPSESSID.';
                    ?>
                </strong></p>
        </section>

        <section class="dashboard-form">
            <h2>Formulir Pesan Musik</h2>

            <form id="albumForm" method="POST" action="../controllers/albums_controller.php?action=create">
                <label>
                    Nama:
                    <input type="text" name="owner_name" id="owner_name" placeholder="Nama pemilik album" required>
                </label>
                <label>
                    Pesan:
                    <textarea name="description" id="description" placeholder="Deskripsi album" required></textarea>
                </label>
                <label>
                    Spotify Embed URL:
                    <input type="text" name="spotify_embed_url" id="spotify_embed_url" placeholder="URL embed Spotify"
                        required>

                    <div class="step-images">
                        <p>Langkah-langkah untuk mendapatkan URL embed Spotify:</p>

                        step-1
                        <img src="../public/assets/step-1.png" alt="step-1" />

                        step-2
                        <img src="../public/assets/step-2.png" alt="step-2" />
                    </div>
                </label>
                <button type="submit" id="submitButton">Tambah Pesan</button>
            </form>

        </section>

        <section class="dashboard-table">
            <h2>Daftar Album Musik</h2>
            <table id="albumTable">
                <thead>
                    <tr>
                        <th>Nama Pemilik</th>
                        <th>Pesan</th>
                        <th>Spotify Embed</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data album akan dimuat menggunakan JavaScript -->
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>