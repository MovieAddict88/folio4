<?php
// src/controllers/admin/documents.php

require_once __DIR__ . '/base.php';
require_once ROOT_PATH . 'src/models/document_model.php'; // We'll create this next

$action = $GLOBALS['admin_action_name'] ?? 'index';
$id = $GLOBALS['admin_param'] ?? null;

switch ($action) {
    case 'index':
        documents_index();
        break;
    case 'upload':
        documents_upload();
        break;
    case 'delete':
        documents_delete($id);
        break;
    case 'set_password':
        documents_set_password($id);
        break;
    default:
        http_response_code(404);
        echo "Action not found in documents controller.";
}

function documents_index() {
    $documents = get_all_documents();
    $data = [
        'title' => 'Manage Documents',
        'documents' => $documents,
        'layout' => 'admin'
    ];
    echo render_view('admin/documents/index', $data);
}

function documents_upload() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['document'])) {
        redirect('admin/documents');
    }

    $file = $_FILES['document'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = ROOT_PATH . 'public/uploads/documents/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $original_filename = basename($file['name']);
        // Sanitize filename to prevent directory traversal attacks
        $safe_filename = preg_replace("/[^a-zA-Z0-9\._-]/", "", $original_filename);
        $filename = uniqid() . '-' . $safe_filename;
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            create_document([
                'filename' => $filename,
                'original_filename' => $original_filename
            ]);
        } else {
            // Handle upload failure, maybe set a flash message
        }
    } else {
        // Handle upload error
    }

    redirect('admin/documents');
}

function documents_delete($id) {
    if (!$id) {
        redirect('admin/documents');
    }

    $doc = get_document_by_id($id);
    if ($doc) {
        $file_path = ROOT_PATH . 'public/uploads/documents/' . $doc['filename'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        delete_document($id);
    }

    redirect('admin/documents');
}

function documents_set_password($id) {
    if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('admin/documents');
    }

    $password = $_POST['password'] ?? '';
    $hashed_password = null;

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    update_document_password($id, $hashed_password);

    redirect('admin/documents');
}