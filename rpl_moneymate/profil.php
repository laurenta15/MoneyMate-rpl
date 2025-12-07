<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$idPengguna = $_SESSION['user']['idPengguna'];
$query = "SELECT * FROM pengguna WHERE idPengguna = $idPengguna";
$result = $conn->query($query);
if (!$result || $result->num_rows === 0) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

// Jika checkbox notifikasi diubah
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aktif = isset($_POST['notifikasiAktif']) ? 1 : 0;
    $conn->query("UPDATE pengguna SET notifikasiAktif = $aktif WHERE idPengguna = $idPengguna");

    // Perbarui session
    $_SESSION['user']['notifikasiAktif'] = $aktif;

    // Refresh halaman agar langsung terlihat
    header("Location: profil.php");
    exit;
}

$data = $result->fetch_assoc();
$foto = !empty($data['fotoProfil']) ? 'uploads/' . $data['fotoProfil'] : 'profil.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Pengguna - MoneyMate</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fdf6ec;
      font-family: 'Segoe UI', sans-serif;
      padding-top: 70px;
    }
    .container {
      max-width: 600px;
    }
    .card {
      background-color: #ffffff;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      border: none;
    }
    .navbar-custom {
      background: linear-gradient(135deg, #fbbd9f, #cde7b0);
    }
    .navbar-brand {
  margin-left: 15px;
  color: #2d3e2f !important; /* Charcoal Green */
  font-weight: bold;
  font-size: 1.4rem;
}
    .btn-edit {
      background-color: #fbbd9f;
      color: #4b3f33;
      border: none;
    }
    .btn-edit:hover {
      background-color: #f5a67e;
      color: #fff;
    }
    .btn-back {
      background-color: #8b9d83;
      color: white;
      border: none;
    }
    .btn-back:hover {
      background-color: #6e8069;
    }
    .profile-pic {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 15px;
      border: 2px solid #ddd;
    }
    .form-label {
      font-weight: 600;
      color: #4b5b45;
    }
    .form-control[readonly] {
      background-color: #f8f9fa;
      border: 1px solid #ced4da;
      color: #495057;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top shadow-sm">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <a href="dashboard.php" class="text-dark text-decoration-none me-2 fs-3">←</a>
      <a class="navbar-brand fw-bold mb-0" href="dashboard.php">MoneyMate</a>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="card text-center">
    <img src="<?= $foto ?>" alt="Foto Profil" class="profile-pic mx-auto">
    <h4 class="mb-4"><?= htmlspecialchars($data['namaPengguna']) ?></h4>

    <form class="text-start">
      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($data['namaPengguna']) ?>" readonly>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" readonly>
      </div>
      <div class="mb-4">
        <label class="form-label">Telepon</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($data['noTelp']) ?>" readonly>
      </div>
    </form>

    <!-- Toggle Notifikasi -->
    <!-- Toggle Notifikasi -->
<form method="post" class="text-start mb-4">
  <div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" id="notifikasiAktif" name="notifikasiAktif" value="1"
      <?= $data['notifikasiAktif'] ? 'checked' : '' ?>
      onchange="this.form.submit()">
    <label class="form-check-label" for="notifikasiAktif">
      Aktifkan Notifikasi Dashboard
    </label>
  </div>
</form>

    <div class="d-flex justify-content-center">
      <a href="edit_profil.php" class="btn btn-edit">Edit Profil</a>
    </div>
  </div>
</div>

</body>
</html>
