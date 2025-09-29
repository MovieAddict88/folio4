<?php
// lib/db.php

/**
 * Creates and returns a PDO database connection object.
 *
 * @return PDO The PDO connection object.
 */
function get_pdo() {
    // This function will be a singleton to ensure only one connection is made.
    static $pdo = null;

    if ($pdo === null) {
        // The config file defines these constants
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In a real application, you'd want to log this error, not just echo it.
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    return $pdo;
}