<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json'); // JSON response

// Validate ID
$material_id = $_POST['id'] ?? null;
if (!$material_id || !is_numeric($material_id)) {
    echo json_encode(['success'=>false, 'message'=>'Invalid material ID']);
    exit();
}

$material_id = (int)$material_id;
$current_user_id = $_SESSION['user_id'] ?? null;
$is_admin = ($_SESSION['role'] ?? '') === 'admin';

if (!$current_user_id) {
    echo json_encode(['success'=>false, 'message'=>'Not logged in']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM tbl_materials WHERE material_id = :id");
    $stmt->execute([':id' => $material_id]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$material) {
        echo json_encode(['success'=>false, 'message'=>'Material not found']);
        exit();
    }

    // Only admin or uploader can delete
    if (!$is_admin && $material['user_id'] != $current_user_id) {
        echo json_encode(['success'=>false, 'message'=>'Access denied']);
        exit();
    }

    // Begin transaction
    $conn->beginTransaction();

    // Delete physical file
    if (!empty($material['file_path']) && file_exists($material['file_path']) && !is_dir($material['file_path'])) {
        unlink($material['file_path']);
    }

    // Delete DB record
    $stmt = $conn->prepare("DELETE FROM tbl_materials WHERE material_id = :id");
    $stmt->execute([':id' => $material_id]);

    $conn->commit();

    echo json_encode(['success'=>true]);

} catch (PDOException $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo json_encode(['success'=>false, 'message'=>'Database error']);
}
