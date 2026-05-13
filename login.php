<?php
// Start the session so the system remembers the login state
session_start();
require 'db.php';

// If already logged in, redirect directly to the admin panel
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error_message = "";

// This part runs when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Look up the user in the database
    $sql = "SELECT * FROM admin_user WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // If the user exists and the password hash matches
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php"); // Redirect to admin.php if login is successful
        exit;
    } else {
        $error_message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
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
        <h2>Admin Login</h2>
        
        <?php if(!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required oninvalid="this.setCustomValidity('Please fill out this field.')" oninput="this.setCustomValidity('')">
            <input type="password" name="password" placeholder="Password" required oninvalid="this.setCustomValidity('Please fill out this field.')" oninput="this.setCustomValidity('')">
            <button type="submit">Log In</button>
        </form>
    </div>
</body>
</html>