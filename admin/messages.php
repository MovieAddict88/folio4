<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/config.php';

$message = '';

// Handle deletion
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $sql = "DELETE FROM messages WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Message deleted successfully!';
        } else {
            $message = 'Error deleting message: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all messages
$sql = "SELECT id, name, email, message, created_at FROM messages ORDER BY created_at DESC";
$messages = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Messages</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 800px; margin: 0 auto; }
        nav { background: #f4f4f4; padding: 1em; }
        nav a { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em;}
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message-body { max-height: 100px; overflow-y: auto; display: block; }
        .message { padding: 1em; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 1em; }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="about.php">About</a>
            <a href="education.php">Education</a>
            <a href="experience.php">Experience</a>
            <a href="skills.php">Skills</a>
            <a href="projects.php">Projects</a>
            <a href="documents.php">Documents</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php" style="float:right;">Logout</a>
        </nav>
        <h1>View Messages</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <h2>Received Messages</h2>
        <table>
            <tr>
                <th>From</th>
                <th>Email</th>
                <th>Message</th>
                <th>Received</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($messages)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
                <td><div class="message-body"><?php echo nl2br(htmlspecialchars($row['message'])); ?></div></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <a href="messages.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>