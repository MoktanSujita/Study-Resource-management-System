<?php
session_start();
include 'config.php';

if(!isset($_SESSION['username'])){
    header("Location: ../templates/login.html");
    exit;
}

$username = $_SESSION['username'];

/* ===== FLASH MESSAGE SYSTEM ===== */
$success = "";
$error = "";

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
<link rel="icon" type="image/png" href="../favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f4f7fc; }

/* Navbar */
.navbar-custom { background-color: #0d6efd; }
.navbar-custom a { color: white !important; font-weight: 500; }
.navbar .dropdown-menu a { color: #212529 !important; }
.navbar .dropdown:hover .dropdown-menu { display: block; margin-top: 0; }

/* Cards */
.card-custom { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }

/* Footer */
.footer { background: #0d6efd; color: white; text-align: center; padding: 15px; margin-top: 40px; }

.btn-active { background-color: #0d6efd !important; color: white !important; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../templates/index.html">Study Resources Management System</a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="../index.php">Home</a></li>

                <!-- NOTES DROPDOWN -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Notes</a>
                    <ul class="dropdown-menu">
                        <?php for($i=1;$i<=8;$i++): ?>
                        <li><a class="dropdown-item" href="view-materials.php?sem=Semester <?php echo $i; ?>&type=Note">Semester <?php echo $i; ?></a></li>
                        <?php endfor; ?>
                    </ul>
                </li>

                <!-- QUESTIONS DROPDOWN -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Questions</a>
                    <ul class="dropdown-menu">
                        <?php for($i=1;$i<=8;$i++): ?>
                        <li><a class="dropdown-item" href="view-materials.php?sem=Semester <?php echo $i; ?>&type=Question">Semester <?php echo $i; ?></a></li>
                        <?php endfor; ?>
                    </ul>
                </li>

                <!--logout -->
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="page-wrapper">
<div class="container mt-4">
    <h2 class="text-center fw-bold mb-4">Student Dashboard</h2>

    <!-- ANNOUNCEMENTS -->
    <h4 class="fw-bold mb-3">Announcements</h4>
    <?php
        try {
            $stmt = $conn->query("SELECT title, message, posted_date FROM tbl_announcement ORDER BY posted_date DESC");
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $shortMessage = strlen($row['message']) > 120 
                                ? substr($row['message'], 0, 120) . '...' 
                                : $row['message'];
                echo '<div class="card-custom">';
                echo '<h6 class="fw-bold">'.htmlspecialchars($row['title']).'</h6>';
                echo '<p class="mt-2">'.htmlspecialchars($shortMessage).'</p>';
                echo '<small class="text-muted">'.date('F j, Y', strtotime($row['posted_date'])).'</small>';
                echo '</div>';
            }
        } catch(PDOException $e){
            echo '<p>Error loading announcements: '.$e->getMessage().'</p>';
        }
    ?>

    <!-- FEEDBACK -->
    <div class="card-custom mt-4">
        <h5>Feedback</h5>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="feedbacks.php" method="POST">
            <div class="mb-3">
                <textarea name="feedback" class="form-control" rows="3" placeholder="Your Feedback" required></textarea>
            </div>
            <button class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
</div>

<!-- FOOTER -->
<div class="footer">
    © 2026 SRMS. All rights reserved.
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
