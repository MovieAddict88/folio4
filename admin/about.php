<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/config.php';

$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];
    $profile_picture = $_POST['current_picture'] ?? ''; // Keep current picture if new one isn't uploaded

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $upload_dir = '../uploads/';
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $new_filename)) {
                $profile_picture = $new_filename;
            } else {
                $message = 'Error uploading file.';
            }
        } else {
            $message = 'Invalid file type.';
        }
    }

    if (empty($message)) { // Only proceed if there were no upload errors
        // Check if about content already exists
        $sql_check = "SELECT id FROM about LIMIT 1";
        $result = mysqli_query($link, $sql_check);

        if (mysqli_num_rows($result) > 0) {
            // Update existing record
            $sql = "UPDATE about SET content = ?, profile_picture = ? WHERE id = (SELECT id FROM about LIMIT 1)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $content, $profile_picture);
        } else {
            // Insert new record
            $sql = "INSERT INTO about (content, profile_picture) VALUES (?, ?)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $content, $profile_picture);
        }

        if (mysqli_stmt_execute($stmt)) {
            $message = 'About section updated successfully!';
        } else {
            $message = 'Error updating about section: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}


// Fetch current about content
$sql = "SELECT content, profile_picture FROM about LIMIT 1";
$result = mysqli_query($link, $sql);
$about = mysqli_fetch_assoc($result);
$content = $about['content'] ?? '';
$current_picture = $about['profile_picture'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - About</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 800px; margin: 0 auto; }
        nav { background: #f4f4f4; padding: 1em; }
        nav a { margin-right: 15px; }
        textarea { width: 100%; height: 200px; }
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
        <h1>Manage About Section</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="about.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="content">About Me Content:</label><br>
                <textarea name="content" id="content" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <br>
            <div>
                <label for="profile_picture">Profile Picture:</label><br>
                <?php if ($current_picture): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($current_picture); ?>" alt="Profile Picture" width="150"><br>
                <?php endif; ?>
                <input type="hidden" name="current_picture" value="<?php echo htmlspecialchars($current_picture); ?>">
                <input type="file" name="profile_picture" id="profile_picture">
            </div>
            <br>
            <div>
                <input type="submit" value="Save Changes">
            </div>
        </form>
    </div>
</body>
</html>