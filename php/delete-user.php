<?php
session_start();
include 'config.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../templates/admin-dashboard.html?error=invalid_id");
    exit();
}

$user_id = (int) $_GET['id'];

// 3️⃣ Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    header("Location: admin-dashboard.php?error=cannot_delete_self");
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM tbl_users WHERE user_id = :id");
    $stmt->execute([':id' => $user_id]);

    header("Location: admin-dashboard.php?success=user_deleted");
    exit();
} catch(PDOException $e) {
    header("Location: admin-dashboard.php?error=db_error");
    exit();
}
?>
