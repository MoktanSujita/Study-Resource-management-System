<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            // Normalize role
            $role = strtolower(trim($user['role']));

            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $role;
            $_SESSION['user_id'] = $user['user_id'];

            // Redirect based on role
            if ($role === 'admin') {
                $_SESSION['admin_id'] = $user['user_id'];
                header("Location: admin-dashboard.php");
            } else {
                header("Location: ../php/student-dashboard.php");
            }
            exit();

        } else {
            $_SESSION['error'] = "Invalid email or password"; // set error
            header("Location: login.php"); // redirect back to login
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="../favicon.png">

  <style>
    body {
      margin: 0;
      background: #e9e9e9;
      font-family: Arial, sans-serif;
    }
    .page-wrapper {
      max-width: 1400px;
      margin: 40px auto;
      background: white;
      min-height: 90vh;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
  </style>
</head>

<body>

<div class="page-wrapper">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-sm-10">
        <div class="card p-4">

          <h3 class="text-center mb-3 fw-bold">Login</h3>

          <!-- Show error if login failed -->
          <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
          <?php endif; ?>

          <form method="post" action="" autocomplete="off">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control mb-3" placeholder="Email" required autocomplete="username">

              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control mb-3" placeholder="Password" required autocomplete="new-password">

              <button type="submit" class="btn btn-primary w-100 py-2 fs-5">Login</button>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>