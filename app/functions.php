<?php
/**
 * This file contains helper functions for the application.
 */

/**
 * Establishes a database connection using PDO.
 * The function is designed to be a singleton, so it only creates one connection per request.
 *
 * @return PDO The PDO database connection object.
 */
function pdo(): PDO
{
    // A static variable to hold the connection instance
    static $pdo_instance = null;

    // If the connection doesn't exist yet, create it
    if ($pdo_instance === null) {
        // The config file must be included to get DB constants
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/../config/config.php';
        }

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo_instance = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In a real application, you would log this error, not display it
            // For development, this is fine.
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    return $pdo_instance;
}

/**
 * A simple helper to get a value from POST superglobal.
 *
 * @param string $key The key to look for in the POST array.
 * @return string|null The value or null if not found.
 */
function get_post(string $key): ?string
{
    return $_POST[$key] ?? null;
}

/**
 * A simple helper for HTML escaping to prevent XSS.
 *
 * @param string|null $string The string to escape.
 * @return string The escaped string.
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>