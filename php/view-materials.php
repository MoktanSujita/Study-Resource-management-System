<?php
session_start();
include 'config.php'; // Ensure this defines $conn as your PDO object

/** * DEBUGGER: If your buttons are hidden, uncomment the line below, 
 * refresh the page, and check if 'user_role' or 'role' says 'admin'.
 */
// print_r($_SESSION); 

// 1. Improved Role Check (Handles common session naming variations)
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'student';
$is_admin = ($user_role === 'admin');

// 2. Capture GET parameters
$selected_semester = isset($_GET['sem']) ? $_GET['sem'] : ''; 
$selected_type = isset($_GET['type']) ? $_GET['type'] : 'Note'; 

$materials = [];
$error_message = '';

// 3. Dynamic Page Title
$page_title = $selected_semester 
    ? htmlspecialchars($selected_semester) . " - " . htmlspecialchars($selected_type)
    : "Select a Semester";

// 4. Database Fetching
if ($selected_semester) {
    try {
        if ($is_admin) {
            // Admin: show all materials for the semester
            $sql = "SELECT * FROM tbl_materials
                    WHERE semester = :sem
                    ORDER BY material_type ASC, subject_name ASC, upload_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':sem' => $selected_semester]);
        } else {
            // Students: show only selected type
            $sql = "SELECT * FROM tbl_materials
                    WHERE semester = :sem AND material_type = :type
                    ORDER BY subject_name ASC, upload_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':sem'  => $selected_semester,
                ':type' => $selected_type
            ]);
        }
        $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        $error_message = "Error fetching data: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
     <link rel="icon" type="image/png" href="../favicon.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
             background-color: #f8f9fa;
             font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
        .materials-container {
             max-width: 950px;
             margin: 40px auto;
             padding: 25px;
             background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .subject-heading { background: #0d6efd; color: white; padding: 12px 18px; border-radius: 8px; margin-top: 30px; font-size: 1.1rem; letter-spacing: 0.5px; }
        .material-card { border: 1px solid #dee2e6; border-left: 5px solid #0d6efd; transition: all 0.2s ease; border-radius: 8px; }
        .material-card:hover { background-color: #f8fbff; transform: translateX(4px); border-color: #0d6efd; }
        .btn-active { background-color: #0d6efd !important; color: white !important; box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3); }
        .file-link { color: #212529; font-weight: 600; text-decoration: none; font-size: 1.05rem; }
        .file-link:hover { color: #0d6efd; }
        .admin-badge { font-size: 0.7rem; padding: 3px 8px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 10px; vertical-align: middle; }
    </style>
</head>
<body>

<div class="container">
    <div class="materials-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php if ($is_admin): ?>
                <a href="../templates/admin-dashboard.html" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Admin Dashboard
                </a>
                <span class="admin-badge"><i class="fa-solid fa-user-shield"></i> Admin View</span>
            <?php else: ?>
                <a href="../php/student-dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Student Dashboard
                </a>
            <?php endif; ?>
        </div>
        
        <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
            <?php for ($i = 1; $i <= 8; $i++): 
                $sem_label = "Semester $i";
                $active_class = ($selected_semester == $sem_label) ? 'btn-active' : 'btn-outline-primary';
            ?>
                <a href="?sem=<?php echo urlencode($sem_label); ?>&type=<?php echo urlencode($selected_type); ?>" 
                   class="btn <?php echo $active_class; ?> btn-sm px-3">
                   Sem <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>

        <div class="d-flex justify-content-between align-items-end">
            <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($page_title); ?></h2>
            <span class="badge bg-light text-dark border"><?php echo count($materials); ?> Files</span>
        </div>
        <hr class="mt-2 mb-4">

        <?php if (!$selected_semester): ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-folder-open fa-3x text-light mb-3"></i>
                <p class="text-muted">Select a semester from the buttons above to browse <?php echo strtolower($selected_type); ?>s.</p>
            </div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error_message; ?></div>
        <?php elseif (empty($materials)): ?>
            <div class="alert alert-warning border-0 bg-light text-center py-4">
                <i class="fa-solid fa-magnifying-glass mb-2 d-block"></i>
                No <?php echo strtolower($selected_type); ?>s found for this semester.
            </div>
        <?php else: ?>
            
            <?php 
            $current_subject = null;
            foreach($materials as $m): 
                // Grouping by Subject Name
                if($m['subject_name'] !== $current_subject):
                    $current_subject = $m['subject_name'];
                    echo "<div class='subject-heading fw-bold shadow-sm'><i class='fa-solid fa-book-open me-2'></i>" . htmlspecialchars($current_subject) . "</div>";
                endif;
            ?>
                <div class="card material-card mt-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            
                            <div class="flex-grow-1">
                                <h5 class="mb-1">
                                    <a href="<?php echo htmlspecialchars($m['file_path']); ?>" target="_blank" class="file-link">
                                        <i class="fa-regular fa-file-pdf text-danger me-1"></i> 
                                        <?php echo htmlspecialchars($m['title']); ?>
                                    </a>
                                </h5>

                                <?php if(!empty($m['description'])): ?>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($m['description']); ?></p>
                                <?php endif; ?>
                                
                                <div class="d-flex gap-3 mt-1">
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fa-regular fa-calendar-check me-1"></i> 
                                        <?php echo date("d M, Y", strtotime($m['upload_date'])); ?>
                                    </small>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-tag me-1"></i> 
                                        <?php echo htmlspecialchars($m['material_type']); ?>
                                    </small>
                                </div>
                            </div>

                            <?php if ($is_admin): ?>
                                <div class="admin-actions d-flex gap-2 ms-3">
                                    <a href="edit-form.php?id=<?php echo $m['material_id']; ?>" 
                                       class="btn btn-warning btn-sm text-white px-3">
                                       <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete-materials.php?id=<?php echo $m['material_id']; ?>" 
                                       class="btn btn-danger btn-sm px-3" 
                                       onclick="return confirm('Permanently delete this file? This cannot be undone.');">
                                       <i class="fa-solid fa-trash-can"></i> Delete
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>