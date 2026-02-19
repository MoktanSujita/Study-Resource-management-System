<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $feedback = trim($_POST['feedback']);
    $user_id  = $_SESSION['user_id'];

    if (empty($feedback)) {

        $_SESSION['error'] = "Feedback cannot be empty.";
        header("Location: student-dashboard.php");
        exit;

    } else {

        try {
            $stmt = $conn->prepare("
                INSERT INTO tbl_feedback (feedback_text, user_id) 
                VALUES (?, ?)
            ");
            $stmt->execute([$feedback, $user_id]);

            $_SESSION['success'] = "Feedback submitted successfully!";
            header("Location: student-dashboard.php");
            exit;

        } catch (PDOException $e) {

            $_SESSION['error'] = "Database Error occurred.";
            header("Location: student-dashboard.php");
            exit;
        }
    }
}

header("Location: student-dashboard.php");
exit;
