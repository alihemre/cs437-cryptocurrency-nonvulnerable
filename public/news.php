<?php
$title = "News";
include './header.php';

function getUserIP() {
    if (!empty($_GET['ip']) && filter_var($_GET['ip'], FILTER_VALIDATE_IP)) {
        return $_GET['ip']; // Validate the IP parameter
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        foreach ($ipList as $ip) {
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

$user_ip = getUserIP();

// Blacklisted IP addresses
$blacklist = ['192.168.1.109', '192.168.56.1', '88.230.79.90'];

if ($user_ip !== "127.0.0.1" && in_array($user_ip, $blacklist)) {
    die("Access denied: $user_ip is blacklisted.");
}

/* ===============================
   FETCH NEWS FROM COINTELEGRAPH RSS
================================= */
$rss_url = "https://cointelegraph.com/rss";
$rss = @simplexml_load_file($rss_url);

$articles = [];

if ($rss && isset($rss->channel->item)) {
    $i = 1;
    foreach ($rss->channel->item as $item) {
        $title    = htmlspecialchars((string) $item->title, ENT_QUOTES, 'UTF-8');
        $summary  = htmlspecialchars((string) $item->description, ENT_QUOTES, 'UTF-8'); // Sanitize RSS content
        $link     = htmlspecialchars((string) $item->link, ENT_QUOTES, 'UTF-8');
        $pubDate  = htmlspecialchars((string) $item->pubDate, ENT_QUOTES, 'UTF-8');

        $articles[] = [
            'id'      => $i++,
            'title'   => $title,
            'summary' => $summary,
            'link'    => $link,
        ];
    }
} else {
    echo "<p style='color:red;'>Unable to load CoinTelegraph RSS feed. Please check the connection.</p>";
}
?>

<!-- CSS Eklemeleri -->
<style>
  /* Your CSS styles remain unchanged */
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
              <!-- Fetch and display image from the RSS summary -->
              <?php
                preg_match('/<img[^>]+src="([^">]+)"/i', $article['summary'], $image_matches);
                $image_url = isset($image_matches[1]) ? htmlspecialchars($image_matches[1], ENT_QUOTES, 'UTF-8') : '';
              ?>
              <?php if ($image_url): ?>
                <img src="<?php echo $image_url; ?>" alt="<?php echo $article['title']; ?>">
              <?php endif; ?>

              <!-- Title -->
              <h3>
                <a href="<?php echo $article['link']; ?>" target="_blank" rel="noopener noreferrer">
                  <?php echo $article['title']; ?>
                </a>
              </h3>

              <!-- Clean summary without <img> tags -->
              <div class="summary">
                <?php
                  $clean_summary = preg_replace('/<img[^>]+>/i', '', $article['summary']);
                  echo $clean_summary;
                ?>
              </div>

              <!-- "Read More" link -->
              <a href="<?php echo $article['link']; ?>" target="_blank" rel="noopener noreferrer" class="read-more">
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
