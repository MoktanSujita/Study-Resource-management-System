<?php

require 'config.php'; // Includes the $pdo connection object

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Triggers the code if the upload button is clicked
    if (isset($_POST['upload'])) {

        // Retrieve and Sanitize Inputs 
        $semester = $_POST['semester'];
        $subjectRaw = $_POST['subject']; // The raw subject name for display/DB

        $subjectFolder = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace(' ', '-', strtolower($subjectRaw)));

        $fileName = basename($_FILES["file"]["name"]);
        $fileTempPath = $_FILES["file"]["tmp_name"]; // Temporary file location

        //  Define Directory ---
        $baseDir = "../uploads/";
        
        $targetDir = $baseDir . $semester . "/" . $subjectFolder . "/";
        $targetfilePath = $targetDir . $fileName; 
        
        //File Type Validation Check 
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); 
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'zip', 'rar']; // Added common types
        
        //create folder if doesnot exist 
        if (!is_dir($targetDir)) {
            // mkdir(path, permissions, recursive_flag)
            if (!mkdir($targetDir, 0777, true)) {
                // If folder creation fails
                die("<script>alert('Error: Failed to create necessary directory structure.');</script>");
            }
        }
        
        //Process Upload ---
        if (in_array($fileType, $allowedTypes)) {
            
            if (move_uploaded_file($fileTempPath, $targetfilePath)) {
                
                try {
                    //sql query to insert into database
                    $sql = "INSERT INTO materials (semester, subject_name, file_name, file_path) 
                            VALUES (:sem, :sub, :fname, :fpath)";
                    $stmt = $pdo->prepare($sql);
                    
                    $stmt->execute([
                        ':sem'   => $semester,
                        ':sub'   => $subjectRaw, // Use raw subject name for display
                        ':fname' => $fileName,
                        ':fpath' => $targetfilePath // Full file path
                    ]);

                    echo "<script>alert('File upload successful!'); window.location.href = '../templates/student-dashboard.html';</script>";
                    
                } catch (PDOException $e) {
                    //database error handling
                    echo "<script>alert('Error: Database failed to save file details.');</script>";
                }

            } else {
                //If failed to move_uploaded_file
                echo "<script>alert('Error: Failed to move the uploaded file. Check server file size limits/permissions.');</script>";
            }

        } else {
            //File type error
            $typeList = strtoupper(implode(', ', $allowedTypes));
            echo "<script>alert('Invalid file type! Please upload one of: $typeList.');</script>";
        }
    }
}
?>