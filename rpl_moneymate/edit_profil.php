<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
include 'koneksi.php';

$id = $_SESSION['user']['idPengguna'];
$query = "SELECT * FROM pengguna WHERE idPengguna = $id";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
  echo "Data tidak ditemukan.";
  exit;
}
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Profil - MoneyMate</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #fbbd9f, #cde7b0);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .edit-box {
      background-color: #fff;
      border-radius: 20px;
      padding: 35px 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 480px;
    }
    .btn-custom {
      background-color: #5c8a63;
      color: white;
      border: none;
    }
    .btn-custom:hover {
      background-color: #6c7d6b;
    }
    .btn-cancel {
      background-color: #d6d6d6;
      color: #333;
      border: none;
    }
    .btn-cancel:hover {
      background-color: #c0c0c0;
    }
  </style>
</head>
<body>

<div class="edit-box">
  <h4 class="mb-4 text-center">Edit Profil</h4>
  <form action="update_profil.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Nama</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($data['namaPengguna']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">No. Telepon</label>
      <input type="text" name="noTelp" value="<?= htmlspecialchars($data['noTelp']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Foto Profil</label>
      <input type="file" name="foto" class="form-control">
    </div>
    <div class="d-flex justify-content-between gap-2">
      <a href="profil.php" class="btn btn-cancel w-50">Batal</a>
      <button type="submit" class="btn btn-custom w-50">Simpan</button>
    </div>
  </form>
</div>

</body>
</html>
