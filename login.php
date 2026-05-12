<?php
// Oturumu başlatıyoruz ki giriş yapıldığını sistem hatırlasın
session_start();
require 'db.php';

// Eğer zaten giriş yapılmışsa, tekrar logine gelmesini engelleyip direkt admin paneline atıyoruz
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$hata_mesaji = "";

// Form gönderildiğinde çalışacak kısım
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = trim($_POST['username']);
    $sifre = trim($_POST['password']);

    // Kullanıcıyı veritabanında ara
    $sql = "SELECT * FROM admin_user WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kullanici_adi]);
    $kullanici = $stmt->fetch();

    // Kullanıcı bulunduysa ve şifre (hash ile) eşleşiyorsa
    if ($kullanici && password_verify($sifre, $kullanici['password'])) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php"); // Giriş başarılıysa admin.php'ye yönlendir
        exit;
    } else {
        $hata_mesaji = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Girişi</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #1e1e2f; margin: 0; }
        .login-container { background: #2a2a40; padding: 30px; border-radius: 10px; box-shadow: 0 8px 16px rgba(0,0,0,0.3); width: 320px; }
        h2 { text-align: center; color: #fff; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #3f3f5a; background-color: #1e1e2f; color: white; border-radius: 5px; box-sizing: border-box; }
        input:focus { outline: none; border-color: #6a5acd; }
        button { width: 100%; padding: 12px; background-color: #6a5acd; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px; margin-top: 10px; }
        button:hover { background-color: #5848c2; }
        .error { color: #ff6b6b; margin-bottom: 10px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Yönetici Girişi</h2>
        
        <?php if(!empty($hata_mesaji)): ?>
            <div class="error"><?php echo $hata_mesaji; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit">Giriş Yap</button>
        </form>
    </div>
</body>
</html>