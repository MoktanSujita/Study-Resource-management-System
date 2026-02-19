<?php
session_start();
include 'config.php'; 'auth.php'; // Ensure this checks for admin access

if(!isset($_SESSION['admin_id'])){
    die("Admin login required.");
}

try {
    $stmt = $conn->query("
        SELECT f.*, u.username 
        FROM tbl_feedback f
        JOIN tbl_users u ON f.user_id = u.user_id
        ORDER BY f.feedback_date DESC
    ");
} catch(PDOException $e){
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Admin Feedback Panel</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../favicon.png">
    </head>

<body>
      <div class="page-wrapper">
         <div class="container mt-5">
             <h2 class="mb-4">Student Feedbacks</h2>
              <a href="admin-dashboard.php" class="btn btn-secondary mb-4">
                 Back to Dashboard
                </a>
              <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

             <div class="card shadow-sm mb-4">
                 <div class="card-body">

                <div class="d-flex justify-content-between">
                    <h6 class="fw-bold mb-1">
                        <?php echo htmlspecialchars($row['username']); ?>
                    </h6>
                    <small class="text-muted">
                        <?php echo date('F j, Y', strtotime($row['feedback_date'])); ?>
                    </small>
                </div>

                <p class="mt-2">
                    <?php echo htmlspecialchars($row['feedback_text']); ?>
                </p>

                <?php if($row['response_text']): ?>

                    <div class="alert alert-success mt-3">
                        <strong>Admin Response:</strong><br>
                        <?php echo htmlspecialchars($row['response_text']); ?><br>
                        <small class="text-muted">
                            <?php echo date('F j, Y', strtotime($row['response_date'])); ?>
                        </small>
                    </div>

                <?php else: ?>

                    <form method="POST" action="response.php" class="mt-3">
                        <input type="hidden" name="feedback_id" 
                               value="<?php echo $row['feedback_id']; ?>">

                        <div class="mb-2">
                            <textarea name="response_text"
                                class="form-control"
                                rows="2"
                                placeholder="Write response..."
                                required></textarea>
                        </div>

                        <button class="btn btn-primary btn-sm">
                            Send Response
                        </button>
                    </form>

                <?php endif; ?>

            </div>
        </div>

       <?php endwhile; ?>

      </div>
    </body>
</html>
