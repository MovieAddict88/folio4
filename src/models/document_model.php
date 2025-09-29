<?php
// src/models/document_model.php

/**
 * Fetches all documents from the database.
 * @return array
 */
function get_all_documents(): array {
    $pdo = get_pdo();
    $stmt = $pdo->query("SELECT * FROM documents ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

/**
 * Fetches a single document by its ID.
 * @param int $id
 * @return array|false
 */
function get_document_by_id(int $id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Creates a new document record in the database.
 * @param array $data
 * @return int
 */
function create_document(array $data): int {
    $pdo = get_pdo();
    $sql = "INSERT INTO documents (filename, original_filename) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['filename'],
        $data['original_filename']
    ]);
    return (int)$pdo->lastInsertId();
}

/**
 * Updates the password for a document.
 * @param int $id
 * @param string|null $password_hash
 * @return bool
 */
function update_document_password(int $id, ?string $password_hash): bool {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("UPDATE documents SET password = ? WHERE id = ?");
    return $stmt->execute([$password_hash, $id]);
}

/**
 * Deletes a document from the database.
 * @param int $id
 * @return bool
 */
function delete_document(int $id): bool {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Fetches a document by filename for the download verification.
 * @param string $filename
 * @return array|false
 */
function get_document_by_filename(string $filename) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE filename = ?");
    $stmt->execute([$filename]);
    return $stmt->fetch();
}