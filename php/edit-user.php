<?php
session_start();
include 'config.php';

//  Admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

//  Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-users.php?error=invalid_id");
    exit();
}

$user_id = (int) $_GET['id'];

//  Fetch user
$stmt = $conn->prepare("SELECT user_id, username, role FROM tbl_users WHERE user_id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: manage-users.php?error=user_not_found");
    exit();
}

//  Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];

    try {
        $stmt = $conn->prepare("UPDATE tbl_users SET role = :role WHERE user_id = :id");
        $stmt->execute([
            ':role' => $role,
            ':id'   => $user_id
        ]);

        header("Location: manage-accounts.php?success=role_updated");
        exit();
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../favicon.png">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Role for <?php echo htmlspecialchars($user['username']); ?></h2>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>{$error}</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select" required>
                <option value="student" <?php if($user['role']=='student') echo 'selected'; ?>>Student</option>
                <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
            </select>
        </div>
        <button class="btn btn-primary">Update Role</button>
        <a href="manage-accounts.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
