<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user = $_SESSION['user'];
$idPengguna = $user['idPengguna'];
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tanggal = $_POST['tanggal'];
  $jenis = $_POST['jenis'];
  $jumlah = $_POST['jumlah'];
  $deskripsi = $_POST['deskripsi'];

  $stmt = $conn->prepare("INSERT INTO transaksi (tanggalTransaksi, jenisTransaksi, jumlah, idPengguna) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssii", $tanggal, $jenis, $jumlah, $idPengguna);

  if ($stmt->execute()) {
    $idTransaksiBaru = $conn->insert_id;

    if ($jenis === 'Pemasukan') {
      $stmt2 = $conn->prepare("INSERT INTO pemasukan (idPengguna, idTransaksi, deskripsiPemasukan) VALUES (?, ?, ?)");
    } else {
      $stmt2 = $conn->prepare("INSERT INTO pengeluaran (idPengguna, idTransaksi, deskripsiPengeluaran) VALUES (?, ?, ?)");
    }

    $stmt2->bind_param("iis", $idPengguna, $idTransaksiBaru, $deskripsi);
    $stmt2->execute();

    header("Location: dashboard.php");
    exit;
  } else {
    $error = "Gagal menambahkan transaksi!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Transaksi - MoneyMate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
    .transaksi-box {
      background-color: #fff;
      border-radius: 20px;
      padding: 35px 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 420px;
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

<div class="transaksi-box">
  <h4 class="mb-4 text-center">Tambah Transaksi</h4>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal</label>
      <input type="date" class="form-control" name="tanggal" id="tanggal" required>
    </div>
    <div class="mb-3">
      <label for="jenis" class="form-label">Jenis Transaksi</label>
      <select class="form-select" name="jenis" id="jenis" required>
        <option value="">-- Pilih --</option>
        <option value="Pemasukan">Pemasukan</option>
        <option value="Pengeluaran">Pengeluaran</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="jumlah" class="form-label">Jumlah (Rp)</label>
      <input type="number" class="form-control" name="jumlah" id="jumlah" required>
    </div>
    <div class="mb-3">
      <label for="deskripsi" class="form-label">Deskripsi</label>
      <textarea class="form-control" name="deskripsi" id="deskripsi" rows="2" placeholder="Contoh: Gaji bulan Juni" required></textarea>
    </div>
    <div class="d-flex justify-content-between gap-2">
      <a href="dashboard.php" class="btn btn-cancel w-50">Batal</a>
      <button type="submit" class="btn btn-custom w-50">Simpan</button>
    </div>
  </form>
</div>

</body>
</html>
