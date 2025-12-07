<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$idPengguna = $_SESSION['user']['idPengguna'];
$tahunIni = date('Y');

// Ambil total pemasukan dan pengeluaran per bulan
$pemasukan = array_fill(1, 12, 0);
$pengeluaran = array_fill(1, 12, 0);

$query = "SELECT MONTH(tanggalTransaksi) AS bulan, SUM(jumlah) AS total, jenisTransaksi
          FROM transaksi
          WHERE idPengguna = $idPengguna AND YEAR(tanggalTransaksi) = $tahunIni
          GROUP BY bulan, jenisTransaksi";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $bulan = (int)$row['bulan'];
    $jenis = $row['jenisTransaksi'];
    $total = (int)$row['total'];
    if ($jenis == 'Pemasukan') {
        $pemasukan[$bulan] = $total;
    } else {
        $pengeluaran[$bulan] = $total;
    }
}

// Ringkasan bulan ini
$bulanIni = date('n');
$pemasukanBulanIni = $pemasukan[$bulanIni];
$pengeluaranBulanIni = $pengeluaran[$bulanIni];
$selisih = $pemasukanBulanIni - $pengeluaranBulanIni;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Keuangan - MoneyMate</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background: #fdf6ec;
      font-family: 'Segoe UI', sans-serif;
      padding-top: 70px;
    }
    .container {
      max-width: 900px;
      margin: auto;
    }
    .navbar-custom {
      background: linear-gradient(135deg, #fbbd9f, #cde7b0);
    }
    .navbar-brand {
      margin-left: 15px;
      color: #2d3e2f !important;
      font-weight: bold;
      font-size: 1.4rem;
    }
    .btn-back {
      background-color: #8b9d83;
      color: white;
      border: none;
    }
    .btn-back:hover {
      background-color: #6d7f68;
    }
    .ringkasan {
      background: #fdf9f4;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
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
  </div>
</nav>

<div class="container mt-4">
  <h3 class="mb-4 text-center">Laporan Keuangan <?= date('Y') ?></h3>

  <canvas id="grafikKeuangan"></canvas>

  <!-- Ringkasan Bulan Ini -->
  <div class="mt-4 ringkasan">
    <h5 class="mb-3">Ringkasan Bulan <?= date('F') ?></h5>
    <div class="row text-center">
      <div class="col-md-4 mb-2">
        <div class="text-muted">Pemasukan</div>
        <div class="text-success fw-bold">Rp <?= number_format($pemasukanBulanIni, 0, ',', '.') ?></div>
      </div>
      <div class="col-md-4 mb-2">
        <div class="text-muted">Pengeluaran</div>
        <div class="text-danger fw-bold">Rp <?= number_format($pengeluaranBulanIni, 0, ',', '.') ?></div>
      </div>
      <div class="col-md-4 mb-2">
        <div class="text-muted">Selisih</div>
        <div class="fw-bold <?= $selisih >= 0 ? 'text-success' : 'text-danger' ?>">Rp <?= number_format($selisih, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
</div>

<script>
  const ctx = document.getElementById('grafikKeuangan');
  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
      datasets: [
        {
          label: 'Pemasukan',
          data: [<?= implode(',', $pemasukan) ?>],
          backgroundColor: '#80c878'
        },
        {
          label: 'Pengeluaran',
          data: [<?= implode(',', $pengeluaran) ?>],
          backgroundColor: '#ffb8b8'
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
</body>
</html>
