<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/config.php';

$message = '';

// Handle form submission for adding a new skill
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_skill'])) {
    $name = $_POST['name'];
    $level = $_POST['level'];

    $sql = "INSERT INTO skills (name, level) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $name, $level);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Skill added successfully!';
        } else {
            $message = 'Error adding skill: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle form submission for updating a skill
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_skill'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $level = $_POST['level'];

    $sql = "UPDATE skills SET name = ?, level = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sii", $name, $level, $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Skill updated successfully!';
        } else {
            $message = 'Error updating skill: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}


// Handle deletion
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $sql = "DELETE FROM skills WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Skill deleted successfully!';
        } else {
            $message = 'Error deleting skill: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all skills
$sql = "SELECT id, name, level FROM skills ORDER BY name ASC";
$skills = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Skills</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 800px; margin: 0 auto; }
        nav { background: #f4f4f4; padding: 1em; }
        nav a { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em;}
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { padding: 1em; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 1em; }
        form { border: 1px solid #ddd; padding: 1em; margin-top: 1em; }
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
        <h1>Manage Skills</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="skills.php" method="post">
            <h2>Add New Skill</h2>
            <input type="hidden" name="add_skill" value="1">
            <div>
                <label for="name">Skill Name:</label><br>
                <input type="text" name="name" id="name" required>
            </div>
            <br>
            <div>
                <label for="level">Proficiency Level (1-100):</label><br>
                <input type="number" name="level" id="level" min="1" max="100" required>
            </div>
            <br>
            <div>
                <input type="submit" value="Add Skill">
            </div>
        </form>

        <h2>Existing Skills</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Level</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($skills)): ?>
            <tr>
                <form action="skills.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="update_skill" value="1">
                    <td><input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required></td>
                    <td><input type="number" name="level" value="<?php echo $row['level']; ?>" min="1" max="100" required></td>
                    <td>
                        <input type="submit" value="Update"> |
                        <a href="skills.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>