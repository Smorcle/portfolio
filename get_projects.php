<?php
require 'db.php';

// Tell the browser and frontend that the data is in JSON format
header('Content-Type: application/json; charset=utf-8');

try {
    // Fetch projects ordered from newest to oldest
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert the data to JSON and output it
    echo json_encode($projects);

} catch (PDOException $e) {
    // If an error occurs, return an error message in JSON format
    echo json_encode(['error' => 'An error occurred while loading projects.']);
}
?>