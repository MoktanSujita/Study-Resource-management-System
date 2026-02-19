<?php
  session_start();

    // Check if user is logged in
   if (!isset($_SESSION['username'])) {
      header("Location:login.php");
      exit;
    }

  // Check if user is admin
   $user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'student';
    if ($user_role !== 'admin') {
       // If not admin, redirect to student dashboard
        header("Location: ../php/student-dashboard.php");
       exit;
    }
?>
