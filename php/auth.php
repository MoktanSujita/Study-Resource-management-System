<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

/**
 * Check login
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Check admin role
 */
function isAdmin(): bool {
    $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? '';
    return $role === 'admin';
}

/**
 * Check ownership (NON-FATAL)
 */
function isMaterialOwner(PDO $conn, int $material_id): bool {
    if (!isLoggedIn() || !isAdmin()) {
        return false;
    }

    $stmt = $conn->prepare(
        "SELECT user_id FROM tbl_materials WHERE material_id = :id"
    );
    $stmt->execute([':id' => $material_id]);
    $owner_id = $stmt->fetchColumn();

    return ($owner_id && $owner_id == $_SESSION['user_id']);
}

/**
 * HARD enforcement (for update/delete)
 */
function requireMaterialOwner(PDO $conn, int $material_id) {
    if (!isMaterialOwner($conn, $material_id)) {
        die("Access denied");
    }
}
