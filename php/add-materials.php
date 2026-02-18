<?php
session_start();
require 'config.php'; // Ensure this defines $conn as your PDO connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {

    // 1. Capture Form Data
    $title = $_POST['title'];
    $sem = $_POST['semester']; // Matches your HTML <select name="semester">
    $subject = $_POST['subject'];
    $type = $_POST['material_type']; 
    $user_id = $_SESSION['user_id'];
    $description = $_POST['description'] ?? '';
    
    // 2. Folder Sanitization
    // Use $subject instead of $subjectRaw (which wasn't defined)
    $subjectFolder = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace(' ', '-', strtolower($subject)));

    // 3. File Info
    $fileName = time() . "_" . basename($_FILES["file"]["name"]); // Added timestamp to prevent overwriting same-named files
    $fileTempPath = $_FILES["file"]["tmp_name"]; 

    // 4. Path Logic
    $baseDir = "../uploads/";
    // Use $sem instead of $semester
    $targetDir = $baseDir . $sem . "/" . $subjectFolder . "/";
    $targetFilePath = $targetDir . $fileName; 
    
    // Create directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // 5. Validation
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); 
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'zip', 'rar']; 
    
    if (in_array($fileType, $allowedTypes)) {
        // 6. Move File and Insert to DB
        if (move_uploaded_file($fileTempPath, $targetFilePath)) {
            try {
                $sql = "INSERT INTO tbl_materials (semester, subject_name, material_type, description, title, file_path, upload_date, user_id) 
                        VALUES (:sem, :sub, :type, :desc, :title, :path, NOW(), :uid)";   
                
                $stmt = $conn->prepare($sql);
                
                $stmt->execute([
                    ':sem'   => $sem,
                    ':sub'   => $subject,
                    ':type'  => $type,
                    ':desc'  => $description, 
                    ':title' => $title,
                    ':path'  => $targetFilePath, // Case sensitive: $targetFilePath
                    ':uid'   => $user_id
                ]);

                echo "<script>alert('File upload successful!'); window.location.href = '../templates/admin-dashboard.html';</script>";
                exit();
                
            } catch (PDOException $e) {
                die("Database Error: " . $e->getMessage());
            }
        } else {
            die("Error: Could not move file to $targetFilePath. Check folder permissions.");
        }
    } else {
        echo "<script>alert('Invalid file type.'); window.history.back();</script>";
    }
} else {
    die("Error: Form was not submitted correctly.");
}
?>