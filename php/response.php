<?php
session_start();
include 'config.php';

if(!isset($_SESSION['admin_id'])){
    die("Admin login required.");
}

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $feedback_id = $_POST['feedback_id'];
    $response_text = trim($_POST['response_text']);

    if(empty($response_text)){
        die("Response cannot be empty.");
    }

    try {
        $stmt = $conn->prepare("
            UPDATE tbl_feedback 
            SET response_text = ?, response_date = NOW()
            WHERE feedback_id = ?
        ");
        $stmt->execute([$response_text, $feedback_id]);

        header("Location: ../templates/admin-feedbacks.html");
        exit;

    } catch(PDOException $e){
        die("Database Error: " . $e->getMessage());
    }
}
?>

