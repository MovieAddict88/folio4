<?php
session_start();
require_once '../config/config.php';
require_once '../app/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    // Basic validation
    if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message)) {
        try {
            $pdo = pdo();
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);

            // Set a success message and redirect
            // In a real app, you might use a more robust flash message system
            $_SESSION['contact_success'] = "Thank you for your message! I will get back to you shortly.";
            header('Location: index.php#contact');
            exit;

        } catch (PDOException $e) {
            // In a real app, log this error
            $_SESSION['contact_error'] = "Sorry, there was an error sending your message. Please try again later.";
            header('Location: index.php#contact');
            exit;
        }
    } else {
        // Invalid data
        $_SESSION['contact_error'] = "Please fill out all fields correctly.";
        header('Location: index.php#contact');
        exit;
    }
} else {
    // Not a post request, just redirect home
    header('Location: index.php');
    exit;
}
?>