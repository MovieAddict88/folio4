<?php
// src/models/portfolio_model.php

/**
 * Fetches all portfolio items from the database.
 * @return array
 */
function get_all_portfolio_items(): array {
    $pdo = get_pdo();
    $stmt = $pdo->query("SELECT * FROM portfolio_items ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

/**
 * Fetches a single portfolio item by its ID.
 * @param int $id
 * @return array|false
 */
function get_portfolio_item_by_id(int $id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Creates a new portfolio item.
 * @param array $data
 * @return int The ID of the newly created item.
 */
function create_portfolio_item(array $data): int {
    $pdo = get_pdo();
    $sql = "INSERT INTO portfolio_items (title, description, image) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['title'],
        $data['description'],
        $data['image']
    ]);
    return (int)$pdo->lastInsertId();
}

/**
 * Updates an existing portfolio item.
 * @param int $id
 * @param array $data
 * @return bool
 */
function update_portfolio_item(int $id, array $data): bool {
    $pdo = get_pdo();
    $sql = "UPDATE portfolio_items SET title = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['title'],
        $data['description'],
        $data['image'],
        $id
    ]);
}

/**
 * Deletes a portfolio item from the database.
 * @param int $id
 * @return bool
 */
function delete_portfolio_item(int $id): bool {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("DELETE FROM portfolio_items WHERE id = ?");
    return $stmt->execute([$id]);
}