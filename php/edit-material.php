<?php
require_once 'auth.php'; // includes session + config automatically

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {

    $id    = $_POST['material_id'];
    $title = $_POST['title'];
    $sem   = $_POST['semester'];

    requireMaterialOwner($conn, $id);

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
