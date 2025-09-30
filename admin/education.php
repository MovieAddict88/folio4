<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/config.php';

$message = '';

// Handle form submission for adding new entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_education'])) {
    $degree = $_POST['degree'];
    $institution = $_POST['institution'];
    $start_year = $_POST['start_year'];
    $end_year = $_POST['end_year'];

    $sql = "INSERT INTO education (degree, institution, start_year, end_year) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssii", $degree, $institution, $start_year, $end_year);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Education entry added successfully!';
        } else {
            $message = 'Error adding entry: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $sql = "DELETE FROM education WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Education entry deleted successfully!';
        } else {
            $message = 'Error deleting entry: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all education entries
$sql = "SELECT id, degree, institution, start_year, end_year FROM education ORDER BY end_year DESC";
$education_entries = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Education</title>
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
        <h1>Manage Education</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="education.php" method="post">
            <h2>Add New Education Entry</h2>
            <input type="hidden" name="add_education" value="1">
            <div>
                <label for="degree">Degree/Certificate:</label><br>
                <input type="text" name="degree" id="degree" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="institution">Institution:</label><br>
                <input type="text" name="institution" id="institution" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="start_year">Start Year:</label><br>
                <input type="number" name="start_year" id="start_year" required>
            </div>
            <br>
            <div>
                <label for="end_year">End Year:</label><br>
                <input type="number" name="end_year" id="end_year" required>
            </div>
            <br>
            <div>
                <input type="submit" value="Add Entry">
            </div>
        </form>

        <h2>Existing Entries</h2>
        <table>
            <tr>
                <th>Degree</th>
                <th>Institution</th>
                <th>Years</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($education_entries)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['degree']); ?></td>
                <td><?php echo htmlspecialchars($row['institution']); ?></td>
                <td><?php echo $row['start_year'] . ' - ' . $row['end_year']; ?></td>
                <td>
                    <a href="education_edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                    <a href="education.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>