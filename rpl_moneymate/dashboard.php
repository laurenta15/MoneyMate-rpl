<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user = $_SESSION['user'];
$id = $user['idPengguna'];
$fotoProfil = !empty($user['fotoProfil']) ? 'uploads/' . $user['fotoProfil'] : 'profil.png';

// Saldo
$query = "
  SELECT 
    (SELECT IFNULL(SUM(jumlah), 0) FROM transaksi WHERE jenisTransaksi = 'Pemasukan' AND idPengguna = $id) - 
    (SELECT IFNULL(SUM(jumlah), 0) FROM transaksi WHERE jenisTransaksi = 'Pengeluaran' AND idPengguna = $id) 
    AS saldo";
$saldo = $conn->query($query)->fetch_assoc()['saldo'];

// Total
$pemasukan = $conn->query("SELECT IFNULL(SUM(jumlah), 0) AS total FROM transaksi WHERE jenisTransaksi = 'Pemasukan' AND idPengguna = $id")->fetch_assoc()['total'];
$pengeluaran = $conn->query("SELECT IFNULL(SUM(jumlah), 0) AS total FROM transaksi WHERE jenisTransaksi = 'Pengeluaran' AND idPengguna = $id")->fetch_assoc()['total'];
$persentase = $pemasukan > 0 ? round(($pengeluaran / $pemasukan) * 100, 2) : 0;

// Transaksi terakhir
$transaksi = $conn->query("SELECT * FROM transaksi WHERE idPengguna = $id ORDER BY tanggalTransaksi DESC LIMIT 3");

// Notifikasi
include 'notifikasi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - MoneyMate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fdf6ec;
      font-family: 'Segoe UI', sans-serif;
      padding-top: 70px;
    }
    .navbar-custom {
      background: linear-gradient(135deg, #fbbd9f, #cde7b0);
    }
    .user-img {
      width: 36px;
      height: 36px;
      object-fit: cover;
      border-radius: 50%;
    }
    .btn-nav {
      margin-right: 10px;
    }
    .card:hover {
      transform: scale(1.01);
      transition: 0.2s;
    }
    .navbar-brand {
  margin-left: 39px;
  color: #2d3e2f !important; 
  font-weight: bold;
  font-size: 1.4rem;
}
    .motivasi-gallery {
      display: flex;
      gap: 1rem;
      overflow-x: auto;
      padding-bottom: 10px;
    }
    .motivasi-img {
      width: 220px;
      height: 220px;
      object-fit: cover;
      border-radius: 16px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      flex-shrink: 0;
      transition: transform 0.2s ease;
    }
    .motivasi-img:hover {
      transform: scale(1.03);
    }
    .alert-warning {
      background-color: #fff8dc;
      border-left: 6px solid #f0ad4e;
      font-weight: 500;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-custom fixed-top shadow-sm">
    <div class="container-fluid">
      <div class="d-flex align-items-center ms-2">
  <a class="navbar-brand fw-bold mb-0" href="#">MoneyMate</a>
</div>
      <div class="ms-auto d-flex align-items-center">
        <a href="tambah_transaksi.php" class="btn btn-success btn-sm btn-nav"><i class="fas fa-plus"></i></a>
        <a href="laporan.php" class="btn btn-primary btn-sm btn-nav"><i class="fas fa-chart-bar"></i></a>
        <a href="riwayat_transaksi.php" class="btn btn-secondary btn-sm btn-nav"><i class="fas fa-clock-rotate-left"></i></a>
        <div class="dropdown ms-2">
          <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <img src="<?= $fotoProfil ?>" class="user-img">
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="profil.php">Profil Saya</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- Notifikasi -->
  <?php if (!empty($notifikasi)): ?>
    <div class="container mt-3">
      <?php foreach ($notifikasi as $msg): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
          <?= $msg ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="container mt-4">
    <div class="card shadow mb-4">
  <div class="card-body">
    <div class="row text-center">
      <div class="col-6 border-end">
        <h6 class="text-muted mb-1">Saldo Anda</h6>
        <h3 class="text-success fw-bold">Rp <?= number_format($saldo, 0, ',', '.') ?></h3>
      </div>
      <div class="col-6">
        <h6 class="text-muted mb-1">Persentase Pengeluaran</h6>
        <h3 class="fw-bold text-danger"><?= $persentase ?>%</h3>
      </div>
    </div>
  </div>
</div>

    <div class="card shadow mb-4">
      <div class="card-header bg-white fw-bold">Transaksi Terakhir</div>
      <div class="card-body">
        <?php if ($transaksi->num_rows > 0): ?>
          <?php while ($row = $transaksi->fetch_assoc()): ?>
            <div class="card mb-2 shadow-sm">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <strong><?= $row['jenisTransaksi'] ?></strong><br>
                  <small class="text-muted"><?= date('d M Y', strtotime($row['tanggalTransaksi'])) ?></small>
                </div>
                <div class="fw-bold <?= $row['jenisTransaksi'] == 'Pemasukan' ? 'text-success' : 'text-danger' ?>">
                  <?= $row['jenisTransaksi'] == 'Pemasukan' ? '+' : '-' ?>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-muted">Belum ada transaksi.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Gambar Motivasi Scroll Horizontal -->
    <div class="card p-3 shadow-sm">
      <div class="text-center fw-bold mb-3">💡 Motivasi Keuangan</div>
      <div class="motivasi-gallery">
        <img src="gambar1.png" class="motivasi-img" alt="Motivasi 1">
        <img src="gambar2.png" class="motivasi-img" alt="Motivasi 2">
        <img src="gambar3.png" class="motivasi-img" alt="Motivasi 3">
        <img src="gambar4.png" class="motivasi-img" alt="Motivasi 4">
        <img src="gambar5.png" class="motivasi-img" alt="Motivasi 5">
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
