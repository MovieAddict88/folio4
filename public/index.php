<?php
// Include necessary files
require_once '../config/config.php';
require_once '../app/functions.php';

// Fetch all data from the database
$pdo = pdo();

// Get profile settings
$profile_settings_raw = $pdo->query("SELECT setting_key, setting_value FROM profile")->fetchAll(PDO::FETCH_KEY_PAIR);
function get_setting($key) {
    global $profile_settings_raw;
    return e($profile_settings_raw[$key] ?? '');
}

// Get all other data
$education = $pdo->query("SELECT * FROM education ORDER BY year DESC")->fetchAll();
$experience = $pdo->query("SELECT * FROM experience ORDER BY year_range DESC")->fetchAll();
$skills = $pdo->query("SELECT * FROM skills ORDER BY type, name")->fetchAll();
$projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC")->fetchAll();
$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
$downloads = $pdo->query("SELECT id, file_name, download_count FROM downloads ORDER BY id DESC")->fetchAll();

// Separate skills into soft and hard for display
$soft_skills = array_filter($skills, fn($s) => $s['type'] === 'soft');
$hard_skills = array_filter($skills, fn($s) => $s['type'] === 'hard');

$page_title = get_setting('hero_title') . " | Portfolio";

// Include the main view file
include '../views/portfolio.php';
?>