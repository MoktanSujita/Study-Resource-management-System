<?php
session_start();
require_once 'config.php'; //  PDO connection

//  Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view-materials.php?error=invalid_id");
    exit();
}

$material_id = (int) $_GET['id'];
$current_user_id = $_SESSION['user_id'] ?? null;
$is_admin = ($_SESSION['role'] ?? '') === 'admin';

if (!$current_user_id) {
    header("Location: ../templates/login.html");
    exit();
}

try {
    //Fetch the material
    $stmt = $conn->prepare("SELECT * FROM tbl_materials WHERE material_id = :id");
    $stmt->execute([':id' => $material_id]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$material) {
        header("Location: view-materials.php?error=material_not_found");
        exit();
    }

    // Ownership check: only admin or uploader can delete
    if (!$is_admin && $material['user_id'] != $current_user_id) {
        header("Location: view-materials.php?error=access_denied");
        exit();
    }

    // Begin transaction
    $conn->beginTransaction();

    // Delete physical file if exists
    if (!empty($material['file_path']) && file_exists($material['file_path']) && !is_dir($material['file_path'])) {
        unlink($material['file_path']);
    }

    // Delete database record
    $stmt = $conn->prepare("DELETE FROM tbl_materials WHERE material_id = :id");
    $stmt->execute([':id' => $material_id]);

    //  Commit transaction
    $conn->commit();

    // Redirect with success
    header("Location: view-materials.php?success=deleted");
    exit();

} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    // Log error in production
    header("Location: view-materials.php?error=db_error");
    exit();
}
?>
