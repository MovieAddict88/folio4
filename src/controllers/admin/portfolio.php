<?php
// src/controllers/admin/portfolio.php

require_once __DIR__ . '/base.php';
require_once ROOT_PATH . 'src/models/portfolio_model.php'; // We'll create this model file next

$action = $GLOBALS['admin_action_name'] ?? 'index';
$id = $GLOBALS['admin_param'] ?? null;

switch ($action) {
    case 'index':
        portfolio_index();
        break;
    case 'add':
        portfolio_form();
        break;
    case 'edit':
        portfolio_form($id);
        break;
    case 'save':
        portfolio_save();
        break;
    case 'delete':
        portfolio_delete($id);
        break;
    default:
        http_response_code(404);
        echo "Action not found in portfolio controller.";
}

function portfolio_index() {
    $items = get_all_portfolio_items();
    $data = [
        'title' => 'Manage Portfolio',
        'items' => $items,
        'layout' => 'admin'
    ];
    echo render_view('admin/portfolio/index', $data);
}

function portfolio_form($id = null) {
    $item = [
        'id' => '',
        'title' => '',
        'description' => '',
        'image' => ''
    ];
    if ($id) {
        $item = get_portfolio_item_by_id($id);
        if (!$item) {
            // Handle not found, maybe set a flash message and redirect
            redirect('admin/portfolio');
        }
    }

    $data = [
        'title' => $id ? 'Edit Portfolio Item' : 'Add Portfolio Item',
        'item' => $item,
        'layout' => 'admin'
    ];
    echo render_view('admin/portfolio/form', $data);
}

function portfolio_save() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('admin/portfolio');
    }

    $id = $_POST['id'] ?? null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';

    // --- Image Upload ---
    $image_path = $_POST['existing_image'] ?? ''; // Keep existing image if no new one is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = ROOT_PATH . 'public/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = uniqid() . '-' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;

        // Basic validation
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($image_type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // If there was an old image and we are updating, delete it
                if ($id && !empty($image_path)) {
                    $old_image_full_path = $upload_dir . $image_path;
                    if (file_exists($old_image_full_path)) {
                        unlink($old_image_full_path);
                    }
                }
                $image_path = $filename;
            } else {
                // Handle upload error, maybe set flash message
            }
        } else {
            // Handle invalid file type
        }
    }

    $data = [
        'title' => $title,
        'description' => $description,
        'image' => $image_path
    ];

    if ($id) {
        update_portfolio_item($id, $data);
    } else {
        create_portfolio_item($data);
    }

    redirect('admin/portfolio');
}

function portfolio_delete($id) {
    if (!$id) {
        redirect('admin/portfolio');
    }

    // First, get the item to delete its image
    $item = get_portfolio_item_by_id($id);
    if ($item && !empty($item['image'])) {
        $image_full_path = ROOT_PATH . 'public/uploads/' . $item['image'];
        if (file_exists($image_full_path)) {
            unlink($image_full_path);
        }
    }

    delete_portfolio_item($id);
    redirect('admin/portfolio');
}