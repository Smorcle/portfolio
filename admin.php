<?php
session_start();
require 'db.php';

// Güvenlik Kontrolü: Oturum açılmamışsa login'e at
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Çıkış Yapma İşlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Proje Silme İşlemi
if (isset($_GET['delete_project'])) {
    $id = $_GET['delete_project'];
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// Mesaj Silme İşlemi
if (isset($_GET['delete_msg'])) {
    $id = $_GET['delete_msg'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// Yeni Proje Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_project'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $project_link = trim($_POST['project_link']);

    $stmt = $pdo->prepare("INSERT INTO projects (title, description, image_url, project_link) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $description, $image_url, $project_link]);
    header("Location: admin.php");
    exit;
}

// Mevcut Verileri Veritabanından Çekme
$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
$messages = $pdo->query("SELECT * FROM messages ORDER BY sent_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetim Paneli</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #1e1e2f; color: #fff; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #3f3f5a; padding-bottom: 10px; margin-bottom: 20px; }
        .logout-btn { background-color: #ff4757; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .container { display: flex; gap: 20px; }
        .card { background: #2a2a40; padding: 20px; border-radius: 10px; flex: 1; }
        h2 { margin-top: 0; color: #6a5acd; }
        input, textarea { width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #3f3f5a; background-color: #1e1e2f; color: white; border-radius: 5px; box-sizing: border-box; }
        button { background-color: #6a5acd; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #5848c2; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #3f3f5a; text-align: left; }
        .action-link { color: #ff4757; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Portfolyo Yönetim Paneli</h1>
        <a href="?logout=true" class="logout-btn">Çıkış Yap</a>
    </div>

    <div class="container">
        <!-- Sol Taraf: Proje Ekleme ve Listesi -->
        <div class="card">
            <h2>Yeni Proje Ekle</h2>
            <form method="POST" action="admin.php">
                <input type="hidden" name="add_project" value="1">
                <input type="text" name="title" placeholder="Proje Başlığı" required>
                <textarea name="description" placeholder="Proje Açıklaması" rows="3"></textarea>
                <input type="text" name="image_url" placeholder="Görsel URL'si (örn: images/proje1.jpg)">
                <input type="text" name="project_link" placeholder="Proje Linki (Github vs.)">
                <button type="submit">Projeyi Kaydet</button>
            </form>

            <h2 style="margin-top: 40px;">Mevcut Projeler</h2>
            <table>
                <tr>
                    <th>Başlık</th>
                    <th>Tarih</th>
                    <th>İşlem</th>
                </tr>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= htmlspecialchars($project['title']) ?></td>
                    <td><?= date('d.m.Y', strtotime($project['created_at'])) ?></td>
                    <td><a href="?delete_project=<?= $project['id'] ?>" class="action-link" onclick="return confirm('Projeyi silmek istediğine emin misin?');">Sil</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($projects)) echo "<tr><td colspan='3'>Henüz proje eklenmemiş.</td></tr>"; ?>
            </table>
        </div>

        <!-- Sağ Taraf: Gelen Mesajlar -->
        <div class="card">
            <h2>Gelen Mesajlar</h2>
            <table>
                <tr>
                    <th>Gönderen</th>
                    <th>Mesaj</th>
                    <th>İşlem</th>
                </tr>
                <?php foreach ($messages as $msg): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($msg['name']) ?></strong><br>
                        <small><?= htmlspecialchars($msg['email']) ?></small>
                    </td>
                    <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td><a href="?delete_msg=<?= $msg['id'] ?>" class="action-link" onclick="return confirm('Mesajı silmek istediğine emin misin?');">Sil</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($messages)) echo "<tr><td colspan='3'>Henüz mesaj yok.</td></tr>"; ?>
            </table>
        </div>
    </div>

</body>
</html>