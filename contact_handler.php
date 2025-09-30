<?php
require_once 'includes/config.php';

$response = ['status' => 'error', 'message' => 'An unexpected error occurred.'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'], $_POST['email'], $_POST['message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message_content = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message_content) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = "INSERT INTO messages (name, email, message) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message_content);
            if (mysqli_stmt_execute($stmt)) {
                $response['status'] = 'success';
                $response['message'] = 'Thank you for your message! I will get back to you shortly.';
            } else {
                $response['message'] = 'Database error. Please try again later.';
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $response['message'] = 'Please fill in all fields correctly.';
    }
} else {
    $response['message'] = 'Invalid request.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>