<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$idPengguna = $_SESSION['user']['idPengguna'];
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: riwayat_transaksi.php");
    exit();
}

$query = "SELECT * FROM transaksi WHERE idTransaksi = $id AND idPengguna = $idPengguna";
$result = $conn->query($query);
if (!$result || $result->num_rows === 0) {
    echo "Transaksi tidak ditemukan.";
    exit();
}
$transaksi = $result->fetch_assoc();
$jenis = $transaksi['jenisTransaksi'];

$deskripsi = '';
if ($jenis == 'Pemasukan') {
    $qDesc = $conn->query("SELECT deskripsiPemasukan AS deskripsi FROM pemasukan WHERE idTransaksi = $id");
} else {
    $qDesc = $conn->query("SELECT deskripsiPengeluaran AS deskripsi FROM pengeluaran WHERE idTransaksi = $id");
}
if ($qDesc && $qDesc->num_rows > 0) {
    $deskripsi = $qDesc->fetch_assoc()['deskripsi'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Transaksi - MoneyMate</title>
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
    .btn-delete {
      background-color: #dc3545;
      color: white;
      border: none;
    }
    .btn-delete:hover {
      background-color: #b52a38;
    }
  </style>
</head>
<body>

<div class="edit-box">
  <h4 class="mb-4 text-center">Edit Transaksi</h4>
  <form method="POST" action="update_transaksi.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal</label>
      <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= $transaksi['tanggalTransaksi'] ?>" required>
    </div>
    <div class="mb-3">
      <label for="jumlah" class="form-label">Jumlah (Rp)</label>
      <input type="number" name="jumlah" id="jumlah" class="form-control" value="<?= $transaksi['jumlah'] ?>" required>
    </div>
    <div class="mb-3">
      <label for="deskripsi" class="form-label">Deskripsi</label>
      <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($deskripsi) ?></textarea>
    </div>
    <div class="d-flex justify-content-between gap-2">
      <button type="submit" class="btn btn-custom w-50">Simpan</button>
      <a href="hapus_transaksi.php?id=<?= $id ?>" class="btn btn-delete w-50" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
    </div>
  </form>
</div>

</body>
</html>
