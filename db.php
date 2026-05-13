<?php
$host = 'localhost';
$dbname = 'my_portfolio'; // This now matches our database name in phpMyAdmin
$username = 'root'; 
$password = '';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connection successful!"; // Remove the // markers if you want to test it
} catch (PDOException $e) {
    die("Connection error: " . $e->getMessage());
}
?>