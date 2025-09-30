<?php
require_once 'includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['doc_id'], $_POST['password'])) {
    $doc_id = $_POST['doc_id'];
    $provided_password = $_POST['password'];

    // Fetch the document details from the database
    $sql = "SELECT filename, filepath, password FROM documents WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $doc_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $document = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($document) {
            // Verify the password
            if (password_verify($provided_password, $document['password'])) {
                $file = 'uploads/' . $document['filepath'];

                if (file_exists($file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($document['filename']) . '.' . pathinfo($document['filepath'], PATHINFO_EXTENSION) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    flush(); // Flush system output buffer
                    readfile($file);
                    exit;
                } else {
                    die('Error: File not found.');
                }
            } else {
                // Redirect back with an error
                header('Location: index.php?download_error=1&doc_id=' . $doc_id . '#download');
                exit;
            }
        } else {
            die('Error: Document not found.');
        }
    }
} else {
    // Redirect if accessed directly
    header('Location: index.php');
    exit;
}
?>