<?php
$servername = "localhost";
$username = "phpuser"; // Eğer özel kullanıcı oluşturduysanız buraya girin.
$password = "securepassword";
$dbname = "news_site";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
