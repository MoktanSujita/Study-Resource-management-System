<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

$current_user_id = $_SESSION['user_id'];

// Fetch student's feedbacks
$stmt = $conn->prepare("
    SELECT * 
    FROM tbl_feedback 
    WHERE user_id = :uid 
    ORDER BY feedback_date DESC
");
$stmt->execute([':uid' => $current_user_id]);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Feedbacks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="../favicon.png">
<style>
body { background-color: #f4f7fc; }

.card-custom {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
}

.alert-info {
    background-color: #cff4fc;
    color: #055160;
}
</style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">My Feedbacks</h2>
    <a href="student-dashboard.php" class="btn btn-secondary mb-4">Back to Dashboard</a>

    <?php if (empty($feedbacks)): ?>
        <div class="alert alert-warning">You have not submitted any feedback yet.</div>
    <?php else: ?>
        <?php foreach ($feedbacks as $f): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <strong>Submitted on:</strong>
                        <small class="text-muted"><?= date('F j, Y', strtotime($f['feedback_date'])) ?></small>
                    </div>

                    <p class="mt-2"><?= nl2br(htmlspecialchars($f['feedback_text'])) ?></p>

                    <?php if (!empty($f['response_text'])): ?>
                        <div class="alert alert-success mt-3">
                            <strong>Admin Response:</strong><br>
                            <?= nl2br(htmlspecialchars($f['response_text'])) ?><br>
                            <small class="text-muted">
                                <?= date('F j, Y', strtotime($f['response_date'])) ?>
                            </small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mt-3">
                            <em>No response yet from admin.</em>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>