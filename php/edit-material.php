<?php
include 'config.php';
require_once 'auth.php'; // includes session + config automatically

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("Invalid material ID");
}

$id = (int)$id;
$current_user_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['role'] ?? '') === 'admin';

// Check owner
$stmt = $conn->prepare("SELECT user_id FROM tbl_materials WHERE material_id = :id");
$stmt->execute([':id' => $id]);
$owner_id = $stmt->fetchColumn();

if (!$is_admin && $owner_id != $current_user_id) {
    die("Unauthorized access");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {

    $id    = $_POST['material_id'];
    $title = $_POST['title'];
    $sem   = $_POST['semester'];

    // Fetch old file path
    $stmt = $conn->prepare(
        "SELECT file_path FROM tbl_materials WHERE material_id = :id"
    );
    $stmt->execute([':id' => $id]);
    $old_path = $stmt->fetchColumn();

    $file_uploaded = !empty($_FILES['material_file']['name']);

    if ($file_uploaded) {

        // Delete old file
        if ($old_path && file_exists($old_path)) {
            unlink($old_path);
        }

        // Upload new file
        $path = "../uploads/" . time() . "_" . basename($_FILES['material_file']['name']);
        move_uploaded_file($_FILES['material_file']['tmp_name'], $path);

        // Update WITH file
        $stmt = $conn->prepare(
            "UPDATE tbl_materials
             SET title = :title,
                 semester = :semester,
                 file_path = :file_path
             WHERE material_id = :id"
        );

        $stmt->execute([
            ':title'     => $title,
            ':semester'  => $sem,
            ':file_path' => $path,
            ':id'        => $id
        ]);

    } else {

        // Update WITHOUT file
        $stmt = $conn->prepare(
            "UPDATE tbl_materials
             SET title = :title,
                 semester = :semester
             WHERE material_id = :id"
        );

        $stmt->execute([
            ':title'    => $title,
            ':semester' => $sem,
            ':id'       => $id
        ]);
    }

    header("Location: view-materials.php?sem=$sem&msg=updated");
    exit;
}
