<?php
$servername = "localhost";
$username = "root"; // MySQL kullanıcı adı
$password = "aliemre3169"; // MySQL root şifresi
$dbname = "news_site"; // Daha önce oluşturduğunuz veritabanı

// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
echo "MySQL bağlantısı başarılı!";
?>
