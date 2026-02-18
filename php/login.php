<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['user_id'];

        if ($user['role'] === 'admin') {
            $_SESSION['admin_id'] = $user['user_id']; // use user_id for admin
            header("Location: ../templates/admin-dashboard.html");
        } else {
            header("Location: ../php/student-dashboard.php");
        }
        exit();

    } else {
        header("Location: ../templates/login.html?error=1");
        exit();
    }
}
?>
