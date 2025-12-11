<?php
session_start();
include 'config.php'; // Includes the database connection ($pdo or $conn)

// 1. Check Admin Role (Used for conditional buttons)
$is_admin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');


// 2. INPUT & PAGE TITLE SETUP
$selected_semester = isset($_GET['sem']) ? htmlspecialchars($_GET['sem']) : ''; // Assigns and sanitizes 'sem' parameter

$materials = [];
$error_message = '';
$page_title = $selected_semester ? $selected_semester : "All Study Materials"; // Assigns dynamic page title


if($selected_semester){
    try{
        // FIX 1: Corrected SQL syntax (ORDERED BY -> ORDER BY)
        $sql = "SELECT id, subject_name, file_name, file_path, uploaded_at 
                FROM materials 
                WHERE semester = :sem 
                ORDER BY subject_name ASC, uploaded_at DESC";

        // FIX 2: Ensure $conn matches the variable used in your config.php (using $pdo here)
        $stmt = $pdo->prepare($sql);

        // Parsing value to the parameter
        $stmt->execute([':sem' => $selected_semester]);

        // Fetch all data from DB
        $materials = $stmt->fetchAll();
        
    }catch(PDOException $e){
        $error_message = "Error fetching data from the database.";
        // In a real application, log $e->getMessage() for debugging.
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>

<div class = "materials-container">
    <h1 class="materials-header"><?php echo htmlspecialchars($page_title); ?> Materials</h1>

    <?php if(isset($error_message)):?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php elseif (empty($materials) && $selected_semester): ?>
        <p> No study materials found for **<?php echo htmlspecialchars($selected_semester);?>** yet.</p>
    <?php elseif (empty($materials) && !$selected_semester): ?>
        <p> Please select a semester to view materials.</p>
    <?php else: ?>
        <?php
          $current_subject = null;
          
          // FIX 3: Corrected loop syntax (foreach($materials as $material):)
          foreach($materials as $material): 
        ?>
          
          <?php 
          // 3. Subject Grouping Logic: Check if subject has changed
          if($material['subject_name'] !== $current_subject):
            if($current_subject !== null):
                echo '</div>'; // Close the previous material-grid container
            endif;
            $current_subject = $material['subject_name'];
          ?>
            <h2 class="subject-heading">📁 <?php echo htmlspecialchars($current_subject); ?></h2>
            <div class="material-grid">
          <?php endif; // End of subject grouping logic ?>

            <div class="card">
                <div class="card-header">
                    <span class="subject-tag"><?php echo htmlspecialchars($material['subject_name']); ?></span>
                </div>
                <div class="card-body">
                    <h4>
                        <a href="<?php echo htmlspecialchars($material['file_path']);?>" target="_blank">
                            <?php echo htmlspecialchars($material['file_name']); ?>
                        </a>
                    </h4>
                    
                    <?php if ($is_admin): ?>
                        <div class="admin-actions">
                            <a href="edit-material.php?id=<?php echo $material['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="delete-material.php?id=<?php echo $material['id']; ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('WARNING: Delete <?php echo addslashes($material['file_name']); ?>?');">
                                Delete
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
                <div class="card-meta">
                    <span class="upload-date">Uploaded: <?php echo date("M d, Y", strtotime($material['uploaded_at'])); ?></span>
                </div>
            </div>

        <?php endforeach; ?>
        
        <?php if($current_subject !== null): echo '</div>'; endif; ?> 

    <?php endif; ?>
</div>

</body>
</html>