<?php
require 'db.php';

// Tarayıcıya ve frontend'e verinin JSON formatında olduğunu söylüyoruz
header('Content-Type: application/json; charset=utf-8');

try {
    // Projeleri en yeniden en eskiye doğru sıralayarak çekiyoruz
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Veriyi JSON'a çevirip ekrana basıyoruz
    echo json_encode($projects);

} catch (PDOException $e) {
    // Bir hata olursa JSON formatında hata mesajı döndürüyoruz
    echo json_encode(['error' => 'Projeler yüklenirken bir hata oluştu.']);
}
?>