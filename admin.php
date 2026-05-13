<?php
session_start();
require 'db.php';

// Security Check: Redirect to login if not signed in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Logout Action
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Delete Project Action
if (isset($_GET['delete_project'])) {
    $id = $_GET['delete_project'];
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// Delete Message Action
if (isset($_GET['delete_msg'])) {
    $id = $_GET['delete_msg'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// Add New Project Action
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

// Fetch Existing Data From Database
$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
$messages = $pdo->query("SELECT * FROM messages ORDER BY sent_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
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
        <h1>Portfolio Admin Panel</h1>
        <a href="?logout=true" class="logout-btn">Log Out</a>
    </div>

    <div class="container">
        <!-- Left Side: Project Add and List -->
        <div class="card">
            <h2>Add New Project</h2>
            <form method="POST" action="admin.php">
                <input type="hidden" name="add_project" value="1">
                <input type="text" name="title" placeholder="Project Title" required oninvalid="this.setCustomValidity('Please fill out this field.')" oninput="this.setCustomValidity('')">
                <textarea name="description" placeholder="Project Description" rows="3"></textarea>
                <input type="text" name="image_url" placeholder="Image URL (e.g. images/project1.jpg)">
                <input type="text" name="project_link" placeholder="Project Link (GitHub etc.)">
                <button type="submit">Save Project</button>
            </form>

            <h2 style="margin-top: 40px;">Existing Projects</h2>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= htmlspecialchars($project['title']) ?></td>
                    <td><?= date('d.m.Y', strtotime($project['created_at'])) ?></td>
                    <td><a href="?delete_project=<?= $project['id'] ?>" class="action-link" onclick="return confirm('Are you sure you want to delete this project?');">Delete</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($projects)) echo "<tr><td colspan='3'>No projects have been added yet.</td></tr>"; ?>
            </table>
        </div>

        <!-- Right Side: Incoming Messages -->
        <div class="card">
            <h2>Incoming Messages</h2>
            <table>
                <tr>
                    <th>Sender</th>
                    <th>Message</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($messages as $msg): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($msg['name']) ?></strong><br>
                        <small><?= htmlspecialchars($msg['email']) ?></small>
                    </td>
                    <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td><a href="?delete_msg=<?= $msg['id'] ?>" class="action-link" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($messages)) echo "<tr><td colspan='3'>No messages yet.</td></tr>"; ?>
            </table>
        </div>
    </div>

</body>
</html>