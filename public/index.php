<?php
$title = "Home";
include './header.php';

//NON-VULNERABLE: BLACKLIST 2
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

// Get the user's IP address
$user_ip = getUserIP();

// Blacklisted IP addresses
$blacklist = ['192.168.1.109', '192.168.56.1', '88.230.79.90']; // Add specific IPs to the blacklist

if ($user_ip !== "127.0.0.1" && in_array($user_ip, $blacklist)) {
    die("Access denied: $user_ip is blacklisted.");
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
