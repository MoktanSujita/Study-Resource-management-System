<?php
require 'auth.php'; // includes session + config

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {

    // 1. Capture Form Data
    $title       = trim($_POST['title']);
    $sem         = trim($_POST['semester']);
    $subject     = trim($_POST['subject']);
    $type        = trim($_POST['material_type']);
    $description = trim($_POST['description'] ?? '');
    $user_id     = $_SESSION['user_id'];

    // 2. Sanitize Folder Name
    $subjectFolder = preg_replace('/[^A-Za-z0-9\-]/', '-', 
                        str_replace(' ', '-', strtolower($subject)));

    // 3. File Info
    $fileName     = time() . "_" . basename($_FILES["file"]["name"]);
    $fileTempPath = $_FILES["file"]["tmp_name"];

    // 4. Path Logic
    $baseDir = "../uploads/";
    $targetDir = $baseDir . $sem . "/" . $subjectFolder . "/";
    $targetFilePath = $targetDir . $fileName;

    // Create directory if not exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // 5. Validate File Type
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'zip', 'rar'];

    if (!in_array($fileType, $allowedTypes)) {
        header("Location: ../templates/upload.html?msg=invalid");
        exit;
    }

    // 6. Move File
    if (!move_uploaded_file($fileTempPath, $targetFilePath)) {
        header("Location: ../templates/upload.html?msg=error");
        exit;
    }

    // 7. Insert into Database
    try {
        $sql = "INSERT INTO tbl_materials 
                (semester, subject_name, material_type, description, title, file_path, upload_date, user_id)
                VALUES (:sem, :sub, :type, :desc, :title, :path, NOW(), :uid)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':sem'   => $sem,
            ':sub'   => $subject,
            ':type'  => $type,
            ':desc'  => $description,
            ':title' => $title,
            ':path'  => $targetFilePath,
            ':uid'   => $user_id
        ]);

        header("Location: ../templates/upload.html?msg=success");
        exit;

    } catch (PDOException $e) {
        header("Location: ../templates/upload.html?msg=dberror");
        exit;
    }
}

header("Location: ../templates/upload.html");
exit;
?>
