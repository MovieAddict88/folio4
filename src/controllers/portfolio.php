<?php
// src/controllers/portfolio.php

// This is the controller for the public-facing site.

require_once ROOT_PATH . 'lib/db.php';
require_once ROOT_PATH . 'lib/helpers.php';
require_once ROOT_PATH . 'src/models/settings_model.php';
require_once ROOT_PATH . 'src/models/portfolio_model.php';
require_once ROOT_PATH . 'src/models/document_model.php';

// The main router in public/index.php directs here by default.
$action = $GLOBALS['action_name'] ?? 'index';
$param = $GLOBALS['param'] ?? null;

switch ($action) {
    case 'index':
        portfolio_show();
        break;
    case 'download':
        // This will handle the protected document downloads
        download_handler($param);
        break;
    default:
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "The page you requested could not be found.";
}


function portfolio_show() {
    $settings = get_all_settings();
    $portfolio_items = get_all_portfolio_items();
    $documents = get_all_documents(); // Get documents to display links

    $data = [
        'title' => $settings['your_name'] ?? 'My Portfolio',
        'settings' => $settings,
        'items' => $portfolio_items,
        'documents' => $documents,
        'layout' => 'public' // Use a new layout for the public site
    ];

    echo render_view('portfolio/index', $data);
}

function download_handler($document_id) {
    if (!$document_id) {
        http_response_code(404);
        die('Document not specified.');
    }

    $doc = get_document_by_id((int)$document_id);

    if (!$doc) {
        http_response_code(404);
        die('Document not found.');
    }

    // If the document is not password protected, serve it directly.
    if (empty($doc['password'])) {
        serve_file($doc);
        return;
    }

    // If it's password protected, handle password submission.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        if (password_verify($password, $doc['password'])) {
            serve_file($doc);
            return;
        } else {
            // Show form with an error message
            $error = 'Invalid password.';
            echo render_view('portfolio/download_form', [
                'doc' => $doc,
                'error' => $error,
                'layout' => 'public'
            ]);
        }
    } else {
        // Show the password form for the first time.
         echo render_view('portfolio/download_form', [
            'doc' => $doc,
            'layout' => 'public'
        ]);
    }
}

function serve_file($doc) {
    $file_path = ROOT_PATH . 'public/uploads/documents/' . $doc['filename'];
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($doc['original_filename']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        http_response_code(404);
        die('File not found on server.');
    }
}