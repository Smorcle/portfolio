<?php
$host = 'localhost';
$dbname = 'my_portfolio'; // Burası artık phpMyAdmin'deki veritabanı adımızla aynı
$username = 'root'; 
$password = '';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Bağlantı başarılı!"; // Test etmek istersen başındaki // işaretlerini kaldırabilirsin
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
?>