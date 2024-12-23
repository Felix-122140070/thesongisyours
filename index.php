<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TheSongsIsYours - Ekspresikan Pesanmu Lewat Musik</title>
  <link rel="stylesheet" href="./public/css/style.css" />
</head>

<body>
  <header class="header">
    <div class="container">
      <a href="index.php" class="logo">TheSongsIsYours</a>
      <p>Platform untuk menyampaikan pesan mendalam melalui musik</p>
    </div>
  </header>

  <nav class="navigation">
    <ul>
      <li><a href="./views/public-albums.php">Jelajahi Pesan</a></li>
      <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']): ?>
        <li><a href="./views/dashboard-albums.php">Dashboard</a></li>
        <li><a href="./controllers/logout.php" class="logout-button">Logout</a></li>
      <?php else: ?>
        <li><a href="./views/register.php">Daftar</a></li>
        <li><a href="./views/login.php">Masuk</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <main class="main-content">
    <section class="hero">
      <h1>Selamat Datang di TheSongsIsYours</h1>
      <p>Ekspresikan pesan yang tak terucapkan melalui musik pilihan Anda</p>
      <a href="./views/dashboard-albums.php" class="cta-button">Tulis Pesan</a>
    </section>

    <section class="features">
      <h2>Kenapa Memilih TheSongsIsYours?</h2>

      <ul>
        <li>Bagikan cerita mendalam dengan lagu yang bermakna.</li>
        <li>Jelajahi dedikasi musik dari seluruh dunia.</li>
        <li>Temukan pesan emosional yang ditulis untuk Anda.</li>
      </ul>
    </section>

    <section class="messages">
      <h2>Dedikasi Terkini</h2>

      <div class="message-list">
        <div class="message">
          <h3>Untuk: Clara</h3>
          <p>“Kenangan perjalanan random kita masih terus hidup abadi di ingatanku.”</p>
          <p><strong>Lagu:</strong> 505 - Arctic Monkeys</p>
          <iframe style="border-radius:12px"
            src="https://open.spotify.com/embed/track/0BxE4FqsDD1Ot4YuBXwAPp?utm_source=generator&theme=0" width="100%"
            height="152" frameBorder="0" allowfullscreen=""
            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
        <div class="message">
          <h3>Untuk: Axel</h3>
          <p>“Aku harap suatu hari aku cukup berani untuk mengungkapkan betapa berartinya dirimu.”</p>
          <p><strong>Lagu:</strong> Pluto Projector - Rex Orange County</p>

          <iframe style="border-radius:12px"
            src="https://open.spotify.com/embed/track/4EWBhKf1fOFnyMtUzACXEc?utm_source=generator&theme=0" width="100%"
            height="152" frameBorder="0" allowfullscreen=""
            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
        <div class="message">
          <h3>Untuk: Lyra</h3>
          <p>“Segalanya mengingatkanku padamu, bahkan warteg tempat kita pertama kali hangout.”</p>
          <p><strong>Lagu:</strong> Head In The Clouds - 88rising</p>

          <iframe style="border-radius:12px"
            src="https://open.spotify.com/embed/track/4xbGmVGcGnCYRTaq2gu6Qr?utm_source=generator&theme=0" width="100%"
            height="152" frameBorder="0" allowfullscreen=""
            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
      </div>
    </section>

    <section class="cta">
      <h2>Siap Menulis Pesanmu?</h2>

      <p>Ekspresikan cerita dan perasaan Anda sekarang melalui lagu favorit!</p>

      <a href="./views/register.php" class="cta-button">Mulai Sekarang</a>
    </section>
  </main>

  <footer class="footer">
    <p>&copy; Copyright TheSongsIsYours 2024</p>
  </footer>
</body>

</html>