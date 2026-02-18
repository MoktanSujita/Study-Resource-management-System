<?php
session_start();
require 'config.php'; 

// Check form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_announcement'])) {

    // Making sure admin is logged in
    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized access");
    }

    // Capture form data
    $title   = trim($_POST['title']);
    $message = trim($_POST['message']);
    $admin_id = $_SESSION['user_id']; 

    try {
        $sql = "INSERT INTO tbl_announcement 
                (title, message, admin_id, posted_date)
                VALUES (:title, :message, :admin_id, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':title'    => $title,
            ':message'  => $message,
            ':admin_id' => $admin_id
        ]);

        echo "<script>
                alert('Announcement posted successfully!');
                window.location.href='../templates/admin-dashboard.html';
              </script>";
        exit();

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}
?>
