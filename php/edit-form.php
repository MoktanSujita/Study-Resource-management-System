<?php
require_once 'auth.php';

$id = $_GET['id'] ?? 0;

// Fetch material
$stmt = $conn->prepare(
    "SELECT * FROM tbl_materials WHERE material_id = :id"
);
$stmt->execute([':id' => $id]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

// Access decision
$hasAccess = $material && isMaterialOwner($conn, $id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../favicon.png">
</head>
<style>
        body { background-color: #f4f7fc; }
        .edit-container { max-width: 550px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    </style>

<div class="container mt-5">

    <!--  ACCESS DENIED MESSAGE -->
    <?php if (!$hasAccess): ?>
        <div class="alert alert-danger">
            <strong>Access Denied </strong><br>
            You are not allowed to edit this material.
        </div>
    <?php endif; ?>

    <!-- (OWNER ONLY) -->
    <?php if ($hasAccess): ?>
       <div class="container">
    <div class="edit-container">
        <h4 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-pen-to-square"></i> Edit Material</h4>
        
        <form action="edit-material.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="material_id" value="<?= $material['material_id'] ?>">

            <div class="mb-3">
                <label class="form-label small fw-bold">Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($material['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Semester</label>
                <select name="semester" class="form-select">
                    <?php for($i=1; $i<=8; $i++): $s = "Semester $i"; ?>
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
</div>
    <?php endif; ?>

</div>

</body>
</html>
