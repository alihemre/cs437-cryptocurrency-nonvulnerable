<?php
session_start(); // Oturumu başlat
session_unset(); // Tüm oturum verilerini sil
session_destroy(); // Oturumu tamamen sonlandır
header('Location: /login.php'); // Login sayfasına yönlendir
exit;
?>
