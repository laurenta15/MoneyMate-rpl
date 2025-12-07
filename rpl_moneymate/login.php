<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($data = $result->fetch_assoc()) {
        $_SESSION['user'] = $data;
        header("Location: dashboard.php");
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - MoneyMate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      height: 100vh;
      margin: 0;
      background: linear-gradient(135deg, #fbbd9f, #cde7b0);
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-box {
      background-color: #fff;
      border-radius: 20px;
      padding: 35px 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 400px;
    }
    .logo {
      text-align: center;
      margin-bottom: 4px;
    }
    .logo img {
      width: 200px;
    }
    .login-box h2 {
      font-size: 14px;
      font-weight: normal;
      text-align: center;
      color: #444;
      margin-bottom: 25px;
    }
    .btn-custom {
      background-color: #5c8a63;
      color: white;
      border: none;
    }
    .btn-custom:hover {
      background-color: #6c7d6b;
    }
    .form-text a {
      text-decoration: none;
      color: #6b6b6b;
    }
    .form-text a:hover {
      color: #4f4f4f;
    }

    @media (max-width: 576px) {
      .login-box {
        padding: 25px 20px;
      }
      .logo img {
        width: 200px;
      }
      .login-box h2 {
        font-size: 16px;
      }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="logo">
      <img src="logo.png" alt="Logo MoneyMate">
    </div>
    <h2>Masuk untuk melanjutkan!</h2>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-custom">Masuk</button>
      </div>
      <div class="form-text text-center">
        <div class="form-text text-center">Belum punya akun? <a href="register.php">Daftar</a>
      </div>
    </form>
  </div>
</body>
</html>
