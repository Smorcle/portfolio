<?php
require 'db.php';

// Only run when a POST request is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Trim leading and trailing spaces from incoming data (for safety and consistency)
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Check whether any fields are empty
    if (!empty($name) && !empty($email) && !empty($message)) {
        if (mb_strlen($name) < 3) {
            echo "Name must be at least 3 characters long.";
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Please enter a valid email address.";
            exit;
        }

        if (mb_strlen($message) < 10) {
            echo "Message must be at least 10 characters long.";
            exit;
        }
        
        $sql = "INSERT INTO messages (name, email, message) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$name, $email, $message]);
            // If successful, return a message for the user to see (or catch with JavaScript)
            echo "Your message has been sent successfully! I will get back to you as soon as possible.";
        } catch (PDOException $e) {
            echo "A system error occurred, and the message could not be sent.";
        }
        
    } else {
        echo "Please fill in all fields.";
    }
} else {
    echo "Invalid request type.";
}
?>