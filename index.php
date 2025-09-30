<?php
require_once 'includes/config.php';

// Fetch About section data
$sql_about = "SELECT * FROM about LIMIT 1";
$result_about = mysqli_query($link, $sql_about);
$about = mysqli_fetch_assoc($result_about);

// Fetch Education data
$sql_education = "SELECT * FROM education ORDER BY end_year DESC";
$education = mysqli_query($link, $sql_education);

// Fetch Experience data
$sql_experience = "SELECT * FROM experience ORDER BY start_date DESC";
$experience = mysqli_query($link, $sql_experience);

// Fetch Skills data
$sql_skills = "SELECT * FROM skills ORDER BY level DESC";
$skills = mysqli_query($link, $sql_skills);

// Fetch Projects data
$sql_projects = "SELECT * FROM projects ORDER BY id DESC";
$projects = mysqli_query($link, $sql_projects);

// Fetch Documents data
$sql_documents = "SELECT id, filename FROM documents ORDER BY id DESC";
$documents = mysqli_query($link, $sql_documents);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfolio</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

    <header>
        <nav>
            <ul>
                <li><a href="#about">About</a></li>
                <li><a href="#experience">Experience</a></li>
                <li><a href="#education">Education</a></li>
                <li><a href="#skills">Skills</a></li>
                <li><a href="#projects">Projects</a></li>
                <li><a href="#download">Download</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php include 'templates/about.php'; ?>
        <?php include 'templates/experience.php'; ?>
        <?php include 'templates/education.php'; ?>
        <?php include 'templates/skills.php'; ?>
        <?php include 'templates/projects.php'; ?>
        <?php include 'templates/download.php'; ?>
        <?php include 'templates/contact.php'; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> - My Portfolio</p>
    </footer>

    <script src="public/js/main.js"></script>
</body>
</html>