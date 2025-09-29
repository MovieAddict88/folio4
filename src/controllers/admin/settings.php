<?php
// src/controllers/admin/settings.php

require_once __DIR__ . '/base.php';
require_once ROOT_PATH . 'src/models/settings_model.php'; // We'll create this next

$action = $GLOBALS['admin_action_name'] ?? 'index';

switch ($action) {
    case 'index':
        settings_index();
        break;
    case 'save':
        settings_save();
        break;
    default:
        http_response_code(404);
        echo "Action not found in settings controller.";
}

function settings_index() {
    $settings = get_all_settings();
    $data = [
        'title' => 'Site Settings',
        'settings' => $settings,
        'layout' => 'admin'
    ];
    echo render_view('admin/settings/index', $data);
}

function settings_save() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('admin/settings');
    }

    $settings_to_save = $_POST['settings'] ?? [];

    // --- Profile Picture Upload ---
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = ROOT_PATH . 'public/uploads/';
        $filename = 'profile-picture-' . uniqid() . '.' . pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $target_file = $upload_dir . $filename;

        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($image_type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // Delete old picture if it exists
                $old_pic = get_setting('profile_picture');
                if ($old_pic) {
                    $old_pic_path = $upload_dir . $old_pic;
                    if (file_exists($old_pic_path)) {
                        unlink($old_pic_path);
                    }
                }
                $settings_to_save['profile_picture'] = $filename;
            }
        }
    }

    foreach ($settings_to_save as $name => $value) {
        set_setting($name, $value);
    }

    // --- Handle password change ---
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($new_password) && $new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $pdo = get_pdo();
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
    }

    redirect('admin/settings');
}