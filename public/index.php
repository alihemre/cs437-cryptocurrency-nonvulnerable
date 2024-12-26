<?php
$title = "Home";
include './header.php';

function getUserIP() {
  if (!empty($_GET['ip'])) {
      return $_GET['ip']; // ip parametresi kontrol ediliyor
  } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      return $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
  } else {
      return $_SERVER['REMOTE_ADDR'];
  }
}

// Kullanıcının IP adresini al
$user_ip = getUserIP();

// Kara listeye alınacak IP adresleri
$blacklist = ['192.168.1.109', '192.168.56.1', '88.230.79.90']; // Arkadaşınızın IP'sini buraya ekleyin

if (!$user_ip === "127.0.0.1" || in_array($user_ip, $blacklist)) {
  die("Erişim yasaklandı: $user_ip adresi kara listededir.");
}

?>

<!-- Hero Section -->
<div class="hero">
  <h2>Stay Ahead in the Crypto World</h2>
  <p>Get the latest news, real-time prices, and in-depth articles on all things crypto.</p>
  <a class="btn" href="news.php">Explore Latest News</a>
</div>

<div class="content">
  <main>
    <h2>Welcome to Crypto News</h2>
    <p>This is your one-stop platform for everything related to cryptocurrency. 
       We provide up-to-date prices, breaking news, and insightful articles.</p>
    <p>Use the navigation above to explore various pages.</p>
  </main>
</div>

<?php include './footer.php'; ?>
