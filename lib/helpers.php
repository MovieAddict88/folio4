<?php
// lib/helpers.php

/**
 * Renders a view file, optionally wrapping it in a layout.
 *
 * @param string $view_name The name of the view file (without .php extension).
 * @param array $data Data to be extracted for use in the view.
 *                    A special key 'layout' can be used to specify a layout file.
 */
function render_view(string $view_name, array $data = []) {
    // Extract the data array into individual variables
    extract($data);

    // Construct the full path to the view file
    $view_path = ROOT_PATH . "views/{$view_name}.php";

    if (file_exists($view_path)) {
        ob_start();
        require $view_path;
        $content = ob_get_clean(); // Get the content of the view

        // If a layout is specified, render the content within the layout
        if (isset($layout)) {
            $layout_path = ROOT_PATH . "views/layouts/{$layout}.php";
            if (file_exists($layout_path)) {
                // The $content variable will be available in the layout file
                ob_start();
                require $layout_path;
                return ob_get_clean();
            } else {
                 trigger_error("Layout '{$layout}' not found.", E_USER_ERROR);
            }
        }

        // If no layout, just return the view content
        return $content;

    } else {
        // Handle cases where the view file doesn't exist
        trigger_error("View '{$view_name}' not found.", E_USER_ERROR);
        return '';
    }
}

/**
 * Escapes HTML for output.
 *
 * @param string|null $string The string to escape.
 * @return string The escaped string.
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirects to a given URL.
 *
 * @param string $url The URL to redirect to.
 */
function redirect(string $url) {
    header("Location: " . BASE_URL . ltrim($url, '/'));
    exit;
}

/**
 * Starts a session if one has not already been started.
 */
function ensure_session_started() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}