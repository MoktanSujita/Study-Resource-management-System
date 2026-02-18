<!DOCTYPE html>
<html>
<head>
  <title>Study Resources Management System</title>
 <link rel="icon" type="image/png" href="favicon.png">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .hero {
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0,0,0,0.7)),
                  url('https://source.unsplash.com/1600x900/?library,study') no-repeat center/cover;
      height: 85vh;
      max-width: 1400px;
      margin: 40px auto;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 80px;
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: 700;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">Study Resources Management System</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="templates/login.html">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="templates/register.html">Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="hero">
  <div class="hero-content">
    <img src="favicon.png" alt="Logo" class="hero-logo">
  <div>
    <h1>Access Study Materials Anytime, Anywhere</h1>
    <p class="mt-3">A complete platform for notes, question papers, announcements.</p>
    <a href="templates/login.html" class="btn btn-primary btn-lg mt-4">Get Started</a>
  </div>
</div>

</body>
</html>
