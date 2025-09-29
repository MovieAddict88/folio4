<?php
// src/controllers/admin/auth.php

require_once ROOT_PATH . 'lib/db.php';
require_once ROOT_PATH . 'lib/helpers.php';

ensure_session_started();

// The router in public/index.php will direct requests like /admin/auth/login here
// Let's use a function based on the action name
$action = $GLOBALS['admin_action_name'] ?? 'login'; // 'login' is the default action

switch ($action) {
    case 'login':
        login_action();
        break;
    case 'logout':
        logout_action();
        break;
    default:
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "Action not found in auth controller.";
}

function login_action() {
    $errors = [];

    // If user is already logged in, redirect to dashboard
    if (isset($_SESSION['user_id'])) {
        redirect('admin/dashboard');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $errors[] = 'Username and password are required.';
        } else {
            $pdo = get_pdo();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                redirect('admin/dashboard');
            } else {
                $errors[] = 'Invalid username or password.';
            }
        }
    }

    // Display the login form
    echo render_view('admin/login', ['errors' => $errors, 'layout' => 'admin_guest']);
}

function logout_action() {
    // Unset all of the session variables
    $_SESSION = [];

    // Destroy the session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Redirect to login page
    redirect('admin/auth/login');
}