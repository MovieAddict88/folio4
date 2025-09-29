<?php
// src/models/settings_model.php

/**
 * Fetches all settings from the database and returns them as an associative array.
 * @return array
 */
function get_all_settings(): array {
    $pdo = get_pdo();
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    foreach ($stmt->fetchAll() as $row) {
        $settings[$row['name']] = $row['value'];
    }
    return $settings;
}

/**
 * Fetches a single setting value by its name.
 * @param string $name
 * @return string|null
 */
function get_setting(string $name): ?string {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = ?");
    $stmt->execute([$name]);
    $result = $stmt->fetchColumn();
    return $result !== false ? $result : null;
}

/**
 * Creates or updates a setting in the database (upsert).
 * @param string $name
 * @param string $value
 * @return bool
 */
function set_setting(string $name, string $value): bool {
    $pdo = get_pdo();
    // ON DUPLICATE KEY UPDATE requires a unique key on the 'name' column, which we have.
    $sql = "INSERT INTO settings (name, value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE value = VALUES(value)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$name, $value]);
}