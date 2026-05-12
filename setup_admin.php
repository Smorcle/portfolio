<?php

require 'db.php';


$kullanici_adi = 'admin'; 
$sifre = 'admin'; 


$hashli_sifre = password_hash($sifre, PASSWORD_DEFAULT);


$sql = "INSERT INTO admin_user (username, password) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);

try {
    
    $stmt->execute([$kullanici_adi, $hashli_sifre]);
    
    echo "<h3 style='color: green;'>Harika! Admin kullanıcısı başarıyla oluşturuldu.</h3>";
    echo "<p>Kullanıcı Adı: <strong>$kullanici_adi</strong></p>";
    echo "<p>Artık <strong>login.php</strong> sayfasına giderek giriş yapabilirsin.</p>";
    echo "<p style='color: red;'><strong>ÖNEMLİ GÜVENLİK ADIMI:</strong> Lütfen işlem bittikten sonra bu dosyayı (setup_admin.php) sil! Aksi takdirde başkaları da çalıştırıp sisteme yeni admin ekleyebilir.</p>";

} catch (PDOException $e) {
    echo "Bir hata oluştu: " . $e->getMessage();
}
?>