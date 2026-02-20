<?php
include 'config.php';
require_once 'auth.php';

$id = $_GET['id'] ?? null;

// Validate ID
if (!$id || !is_numeric($id)) {
    die("<div class='alert alert-danger text-center mt-5'>Invalid material ID</div>");
}
$id = (int)$id;

// Fetch material
$stmt = $conn->prepare("SELECT * FROM tbl_materials WHERE material_id = :id");
$stmt->execute([':id' => $id]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

$current_user_id = $_SESSION['user_id'] ?? null;
$is_admin = ($_SESSION['role'] ?? '') === 'admin';

// Check access: only admin who uploaded can edit
$hasAccess = $material && $is_admin && $material['user_id'] == $current_user_id;

// Handle POST (form submission)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update']) && $hasAccess) {
    $title = $_POST['title'];
    $semester = $_POST['semester'];

    // Get old file
    $stmt = $conn->prepare("SELECT file_path FROM tbl_materials WHERE material_id = :id");
    $stmt->execute([':id' => $id]);
    $old_path = $stmt->fetchColumn();

    $file_uploaded = !empty($_FILES['material_file']['name']);
    if ($file_uploaded) {
        // Delete old file
        if ($old_path && file_exists($old_path)) {
            unlink($old_path);
        }
        // Upload new file
        $new_path = "../uploads/" . time() . "_" . basename($_FILES['material_file']['name']);
        move_uploaded_file($_FILES['material_file']['tmp_name'], $new_path);

        $stmt = $conn->prepare("
            UPDATE tbl_materials
            SET title = :title, semester = :semester, file_path = :file_path
            WHERE material_id = :id
        ");
        $stmt->execute([
            ':title' => $title,
            ':semester' => $semester,
            ':file_path' => $new_path,
            ':id' => $id
        ]);
    } else {
        $stmt = $conn->prepare("
            UPDATE tbl_materials
            SET title = :title, semester = :semester
            WHERE material_id = :id
        ");
        $stmt->execute([
            ':title' => $title,
            ':semester' => $semester,
            ':id' => $id
        ]);
    }

    header("Location: view-materials.php?sem=$semester&msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Material</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../favicon.png">
    <style>
        body { background-color: #f4f7fc; }
        .edit-container {
            max-width: 550px; 
            margin: 50px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.05); 
        }
    </style>
</head>
<body>
<div class="container mt-5">

<?php if (!$hasAccess): ?>
    <div class="alert alert-danger text-center">
        <strong>Access Denied</strong><br>
        You are not allowed to edit this material.
    </div>
<?php else: ?>
    <div class="edit-container">
        <h4 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-pen-to-square"></i> Edit Material</h4>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="material_id" value="<?= $material['material_id'] ?>">

            <div class="mb-3">
                <label class="form-label small fw-bold">Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($material['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Semester</label>
                <select name="semester" class="form-select">
                    <?php for ($i = 1; $i <= 8; $i++): 
                        $s = "Semester $i"; ?>
                        <option value="<?= $s ?>" <?= $material['semester'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">File Replacement</label>
                <div class="p-2 border rounded bg-light mb-2 small text-muted">
                    <i class="fa-solid fa-file"></i> <?= basename($material['file_path']) ?>
                </div>
                <input type="file" name="material_file" class="form-control form-control-sm">
            </div>

            <div class="d-flex gap-2 pt-3">
                <button type="submit" name="update" class="btn btn-primary w-100 fw-bold">Update</button>
                <a href="view-materials.php" class="btn btn-light w-100">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>