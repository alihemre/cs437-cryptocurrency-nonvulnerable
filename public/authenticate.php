<?php
session_start(); // Kullanıcı oturumunu başlat

// Blacklist Tanımı
$blacklist = ['10.211.55.8', '10.211.55.0/24'];

// IP Adresini Kontrol Eden Fonksiyon
function is_ip_blacklisted($ip, $blacklist) {
    foreach ($blacklist as $blocked_ip) {
        if (strpos($blocked_ip, '/') !== false) {
            // CIDR Aralığını Kontrol Et
            list($subnet, $bits) = explode('/', $blocked_ip);
            $ip_binary = ip2long($ip);
            $subnet_binary = ip2long($subnet);
            $mask = ~((1 << (32 - $bits)) - 1);
            if (($ip_binary & $mask) === ($subnet_binary & $mask)) {
                return true;
            }
        } else {
            // Doğrudan IP Eşleşmesi
            if ($ip === $blocked_ip) {
                return true;
            }
        }
    }
    return false;
}

// Kullanıcının IP Adresini Al
$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

// IP Adresi Kara Listede mi?
if (is_ip_blacklisted($user_ip, $blacklist)) {
    http_response_code(403); // Erişim Reddedildi
    echo "Access denied: Your IP ($user_ip) is blacklisted.";
    exit;
}

// Örnek kullanıcı bilgileri (gerçek bir veritabanıyla değiştirilmeli)
$users = [
    'admin' => ['password' => 'admin123', 'role' => 'admin'],
    'user' => ['password' => 'user123', 'role' => 'user']
];

// Formdan gelen kullanıcı bilgilerini al
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Kullanıcı adı doğrulaması
if (isset($users[$username]) && $users[$username]['password'] === $password) {
    // Giriş başarılı, oturum verilerini ayarla
    $_SESSION['user_id'] = $username;
    $_SESSION['role'] = $users[$username]['role'];
    header('Location: /about.php'); // Başarılı girişten sonra yönlendirme
    exit;
} else {
    // Giriş başarısız
    header('Location: /login.php?error=1');
    exit;
}
?>
