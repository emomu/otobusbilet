<?php
// config.php - Veritabanı ve diğer yapılandırma ayarları
define('DB_SERVER', 'localhost:8889'); // MAMP için port 8889 eklendi
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // MAMP'ta MySQL şifresi genellikle 'root'tur
define('DB_NAME', 'otobusbilet');

// MySQL veritabanına bağlantı
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Bağlantıyı kontrol et
if($conn === false){
    die("HATA: Veritabanına bağlanılamadı. " . mysqli_connect_error());
}

// Oturum başlat
session_start();

// Tarih formatı için fonksiyon
function formatDate($date) {
    $date = new DateTime($date);
    return $date->format('d.m.Y H:i');
}

// PNR kodu oluşturan fonksiyon
function generatePNR() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

// Güvenli input temizleme fonksiyonu
function cleanInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}
?>