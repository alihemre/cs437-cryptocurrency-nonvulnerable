<?php
$title = "News";
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

// ip=1 ile kontrolü atlatabilmek için özel bir kontrol ekleyelim
if (!$user_ip === "127.0.0.1" || in_array($user_ip, $blacklist)) {
  die("Erişim yasaklandı: $user_ip adresi kara listededir.");
}

/* ===============================
   FETCH NEWS FROM COINTELEGRAPH RSS
================================= */
$rss_url = "https://cointelegraph.com/rss";  // CoinTelegraph’s RSS feed
$rss = @simplexml_load_file($rss_url);       // Use '@' to suppress warnings if feed fails

$articles = []; // This will hold the parsed RSS items

if ($rss && isset($rss->channel->item)) {
    // Build an $articles array similar to your previous structure
    $i = 1;
    foreach ($rss->channel->item as $item) {
        $title    = (string) $item->title;
        $summary  = (string) $item->description;  // Short description from the RSS
        $link     = (string) $item->link;
        $pubDate  = (string) $item->pubDate;      // e.g. "Mon, 09 Oct 2023 08:00:00 +0000"

        // Push into $articles array
        $articles[] = [
            'id'      => $i++,
            'title'   => $title,
            'summary' => $summary,
            'link'    => $link,
            // 'date' => $formattedDate, // (optional if you want to show formatted date)
        ];
    }
} else {
    // RSS feed couldn’t be loaded or is invalid
    // Optionally display an error message or leave $articles empty
    echo "<p style='color:red;'>CoinTelegraph RSS feed alınamadı. Lütfen bağlantıyı kontrol edin.</p>";
    // $articles stays empty
}
?>

<!-- CSS Eklemeleri -->
<style>
  /* Genel Düzenlemeler */
  .news-wrapper {
    display: flex;
    flex-direction: column; /* Flex yönü tek sütun */
    gap: 20px;
  }

  /* Haber Kartlarının Stili */
  .news-content {
    width: 100%; /* Genişlik ayarlandı */
  }

  .news-list {
    display: flex;
    flex-wrap: wrap; /* Kartların satırları sarmasını sağlar */
    gap: 20px;
  }

  .news-card {
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 5px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    width: calc(50% - 10px); /* İki sütun için genişlik ayarı */
    box-sizing: border-box; /* Padding ve border'ın toplam genişliğe dahil edilmesi */
  }

  .news-card h3 {
    margin-top: 0;
    font-size: 1.2em; /* Başlık boyutunu biraz küçülttük */
    margin-bottom: 10px;
  }

  .news-card .summary {
    flex: 1;
    margin-bottom: 10px;
    font-size: 0.95em; /* Özet metin boyutunu biraz küçülttük */
    line-height: 1.4;
  }

  .news-card img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 10px 0;
    border-radius: 5px;
  }

  .read-more {
    align-self: flex-start;
    padding: 8px 12px;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
    font-size: 0.9em; /* Buton metin boyutunu biraz küçülttük */
  }

  .read-more:hover {
    background-color: #0056b3;
  }

  /* Responsive Düzenlemeler */
  @media (max-width: 1200px) {
    .news-card {
      width: calc(50% - 10px); /* Geniş ekranlarda iki sütun */
    }
  }

  @media (max-width: 768px) {
    .news-card {
      width: 100%; /* Tablet ve mobilde tek sütun */
    }
  }
</style>

<div class="content">
  <main class="news-wrapper">
    
    <!-- Main News Content -->
    <div class="news-content">
      <h2>Cryptocurrency News (CoinTelegraph)</h2>

      <div class="news-list">
        <?php if (!empty($articles)): ?>
          <?php foreach ($articles as $article): ?>
            <div class="news-card">
              <!-- Görsel -->
              <?php
                // RSS summary'sinden görsel URL'sini çekmek için basit bir regex kullanıyoruz
                preg_match('/<img[^>]+src="([^">]+)"/i', $article['summary'], $image_matches);
                $image_url = isset($image_matches[1]) ? $image_matches[1] : '';
              ?>
              <?php if ($image_url): ?>
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
              <?php endif; ?>

              <!-- Title -->
              <h3>
                <!-- Link to CoinTelegraph article in new tab -->
                <a href="<?php echo htmlspecialchars($article['link']); ?>" target="_blank" rel="noopener noreferrer">
                  <?php echo htmlspecialchars($article['title']); ?>
                </a>
              </h3>

              <!-- Summary from RSS -->
              <div class="summary">
                <?php 
                  // Özet içerisindeki img etiketlerini kaldır
                  $clean_summary = preg_replace('/<img[^>]+>/i', '', $article['summary']);
                  echo $clean_summary; 
                ?>
              </div>

              <!-- "Read More" link to direct user to the original article -->
              <a href="<?php echo htmlspecialchars($article['link']); ?>" target="_blank" rel="noopener noreferrer" class="read-more">
                Read More &rarr;
              </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No news found or RSS could not be loaded.</p>
        <?php endif; ?>
      </div>
    </div>

  </main>
</div>

<?php include './footer.php'; ?>
