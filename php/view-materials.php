<?php
session_start();
include 'config.php'; // make sure login check exists

$role = $_SESSION['role'] ?? 'student';
$is_admin = ($role === 'admin');

$selected_semester = $_GET['sem'] ?? '';
$selected_type = $_GET['type'] ?? 'Notes';
$materials = [];
$error_message = '';

$page_title = $selected_semester 
    ? $selected_semester . " - " . $selected_type
    : "Select a Semester";

// Fetch materials
if ($selected_semester) {
    try {
        if ($is_admin) {
            $stmt = $conn->prepare("SELECT * FROM tbl_materials WHERE semester=:sem ORDER BY material_type ASC, subject_name ASC, upload_date DESC");
            $stmt->execute([':sem'=>$selected_semester]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM tbl_materials WHERE semester=:sem AND material_type=:type ORDER BY subject_name ASC, upload_date DESC");
            $stmt->execute([':sem'=>$selected_semester, ':type'=>$selected_type]);
        }
        $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e){
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title); ?></title>
<link rel="icon" type="image/png" href="../favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.materials-container { max-width: 950px; margin: 40px auto; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.subject-heading { background: #0d6efd; color: white; padding: 12px 18px; border-radius: 8px; margin-top: 30px; font-size: 1.1rem; }
.material-card { border: 1px solid #dee2e6; border-left:5px solid #0d6efd; border-radius:8px; transition:all 0.2s; }
.material-card:hover { background-color:#f8fbff; transform:translateX(4px); border-color:#0d6efd; }
.btn-active { background-color:#0d6efd !important; color:white !important; }
.file-link { color:#212529; font-weight:600; text-decoration:none; }
.file-link:hover { color:#0d6efd; }
.footer { background: #0d6efd; color:white; text-align:center; padding:15px; margin-top:40px; }
</style>
</head>
<body>

<div class="container materials-container">

    <!-- BACK -->
    <div class="mb-4">
        <?php if($is_admin): ?>
            <a href="admin-dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Admin Dashboard</a>
        <?php else: ?>
            <a href="student-dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Student Dashboard</a>
        <?php endif; ?>
    </div>

    <!-- SEMESTER BUTTONS -->
    
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <?php for($i=1;$i<=8;$i++):
            $sem_label = "Semester $i";
            $active_class = ($selected_semester==$sem_label)
            ? 'btn-primary'
            : 'btn-outline-primary';
        ?>
            <a href="view-materials.php?sem=<?= urlencode($sem_label) ?>&type=<?= urlencode($selected_type) ?>" 
                class="btn btn-sm <?= $active_class ?>">
                  Sem <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>

    <h2 class="fw-bold mb-3"><?= htmlspecialchars($page_title) .'s'?></h2>
    <hr>

    <?php if(!$selected_semester): ?>
        <p>Select a semester above to see materials.</p>
    <?php elseif($error_message): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php elseif(empty($materials)): ?>
        <div class="alert alert-warning">No <?= strtolower($selected_type) ?> found for this semester.</div>
    <?php else: 
        $current_subject = '';
        foreach($materials as $m):
            if($m['subject_name'] !== $current_subject):
                $current_subject = $m['subject_name'];
                echo "<div class='subject-heading'>" . htmlspecialchars($current_subject) . "</div>";
            endif;
    ?>
        <div class="card material-card mt-2 p-3">
    <div class="d-flex justify-content-between align-items-start">

        <!-- LEFT SIDE: File Info -->
        <div>
            <a href="<?= htmlspecialchars($m['file_path']) ?>" target="_blank" class="file-link">
                <i class="fa-regular fa-file-pdf text-danger me-1"></i>
                <?= htmlspecialchars($m['title']) ?>
            </a>

            <?php if(!empty($m['description'])): ?>
                <p class="text-muted small mb-0">
                    <?= htmlspecialchars($m['description']) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- RIGHT SIDE: Admin Buttons -->
        <?php if($is_admin): ?>
            <div class="d-flex gap-2">
                <a href="edit-form.php?id=<?= $m['material_id'] ?>" 
                   class="btn btn-sm btn-outline-primary">
                   <i class="fa-solid fa-pen"></i>
                </a>

                <button class="btn btn-sm btn-outline-danger delete-btn"
                  data-id="<?= $m['material_id'] ?>">
                   <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        <?php endif; ?>

    </div>
</div>
    <?php endforeach; endif; ?>

</div>

<div class="footer">© 2026 SRMS. All rights reserved.</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {

        if (!confirm("Are you sure you want to delete this material?")) {
            return;
        }

        let id = this.dataset.id;

        fetch('delete-materials.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });
});
</script>
</body>
</html>