<?php
require 'db.php';

// Sadece POST isteği geldiğinde çalışsın
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Gelen verilerin başındaki ve sonundaki boşlukları temizliyoruz (güvenlik ve düzen için)
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Alanların boş olup olmadığını kontrol ediyoruz
    if (!empty($name) && !empty($email) && !empty($message)) {
        
        $sql = "INSERT INTO messages (name, email, message) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$name, $email, $message]);
            // Başarılı olursa kullanıcıyı bilgilendirecek bir metin döndür (veya Javascript ile yakala)
            echo "Mesajınız başarıyla gönderildi! En kısa sürede dönüş yapacağım.";
        } catch (PDOException $e) {
            echo "Sistemsel bir hata oluştu, mesaj gönderilemedi.";
        }
        
    } else {
        echo "Lütfen tüm alanları doldurun.";
    }
} else {
    echo "Geçersiz istek türü.";
}
?>