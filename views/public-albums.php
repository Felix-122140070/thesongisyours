<?php
// Koneksi ke database
include '../config/db_config.php';

session_start();

// Inisialisasi variabel pencarian
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Query untuk mengambil data album
if (!empty($searchTerm)) {
    $stmt = $conn->prepare("SELECT owner_name, description, spotify_embed_url FROM music_albums WHERE owner_name LIKE ?");
    $searchWildcard = '%' . $searchTerm . '%';
    $stmt->bind_param("s", $searchWildcard);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT owner_name, description, spotify_embed_url FROM music_albums";
    $result = $conn->query($query);
}

// Periksa apakah data ditemukan
$albums = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $albums[] = $row;
    }
}

// Tutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi Pesan Musik - TheSongsIsYours</title>
    <link rel="stylesheet" href="../public/css/style.css">
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
            <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']): ?>
                <li><a href="dashboard-albums.php">Dashboard</a></li>
                <li><a href="../controllers/logout.php" class="logout-button">Logout</a></li>
            <?php else: ?>
                <li><a href="register.php">Daftar</a></li>
                <li><a href="login.php">Masuk</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="main-content">
        <section class="hero">
            <h1>Jelajahi Pesan Musik</h1>
            <form method="GET" action="public-albums.php" class="search-form">
                <input type="text" name="search" placeholder="Cari berdasarkan nama pemilik..."
                    value="<?php echo $searchTerm; ?>" />
                <button type="submit" class="cta-button">Cari</button>
            </form>
        </section>

        <section class="albums">
            <?php if (!empty($albums)): ?>
                <?php foreach ($albums as $album): ?>
                    <div class="album">
                        <h2>To: <?php echo htmlspecialchars($album['owner_name']); ?></h2>

                        <p>From: Anonymous</p>
                        <p>Message: <?php echo htmlspecialchars($album['description']); ?></p>
                        <div class="spotify-embed">
                            <?php echo htmlspecialchars_decode($album['spotify_embed_url']); ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada pesan musik ditemukan.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; Copyright TheSongsIsYours 2024</p>
    </footer>
</body>

</html>