<?php
// logout.php - Çıkış sayfası
session_start();
 
// Tüm oturum değişkenlerini sıfırla
$_SESSION = array();
 
// Oturumu yoket
session_destroy();
 
// Giriş sayfasına yönlendir
header("location: login.php");
exit;
?>