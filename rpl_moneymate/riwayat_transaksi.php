<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$idPengguna = $_SESSION['user']['idPengguna'];

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

$query = "
  SELECT * FROM transaksi 
  WHERE idPengguna = $idPengguna 
  AND MONTH(tanggalTransaksi) = $bulan 
  AND YEAR(tanggalTransaksi) = $tahun 
  ORDER BY tanggalTransaksi DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Transaksi - MoneyMate</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #fdf6ec;
      font-family: 'Segoe UI', sans-serif;
      padding-top: 70px;
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
    .table {
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .table th {
      background-color: #cde7b0;
      color: #333;
    }
    .table td {
      vertical-align: middle;
    }
    .badge-pemasukan {
      background-color: #88d18a;
    }
    .badge-pengeluaran {
      background-color: #f4a6a6;
    }
    .btn-back {
      background-color: #8b9d83;
      color: white;
      border: none;
    }
    .btn-back:hover {
      background-color: #6e8069;
    }
  </style>
  <script>
    function filterTable() {
      var input = document.getElementById("searchInput");
      var filter = input.value.toLowerCase();
      var rows = document.querySelectorAll("table tbody tr");

      rows.forEach(function(row) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    }
  </script>
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

<div class="container mt-4 px-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Riwayat Transaksi</h3>
    <a href="tambah_transaksi.php" class="btn btn-success">+</a>
  </div>

  <!-- Filter Bulan dan Tahun -->
  <form method="GET" class="row g-2 align-items-center mb-3">
    <div class="col-auto">
      <select name="bulan" class="form-select">
        <?php
        $bulanNama = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        for ($i = 1; $i <= 12; $i++) {
          $selected = ($i == $bulan) ? 'selected' : '';
          echo "<option value='$i' $selected>{$bulanNama[$i-1]}</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-auto">
      <select name="tahun" class="form-select">
        <?php
        $tahunSekarang = date('Y');
        for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--) {
          $selected = ($i == $tahun) ? 'selected' : '';
          echo "<option value='$i' $selected>$i</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-success">Tampilkan</button>
    </div>
  </form>

  <!-- Search box -->
  <div class="mb-3">
    <input type="text" id="searchInput" onkeyup="filterTable()" class="form-control" placeholder="Cari transaksi... (tanggal, jenis, deskripsi, jumlah)">
  </div>

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="text-center">
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Jenis</th>
          <th>Jumlah</th>
          <th>Deskripsi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
            <?php
              $idTransaksi = $row['idTransaksi'];
              $jenis = $row['jenisTransaksi'];
              $deskripsi = '-';
              if ($jenis == 'Pemasukan') {
                $descQuery = $conn->query("SELECT deskripsiPemasukan AS deskripsi FROM pemasukan WHERE idTransaksi = $idTransaksi");
              } else {
                $descQuery = $conn->query("SELECT deskripsiPengeluaran AS deskripsi FROM pengeluaran WHERE idTransaksi = $idTransaksi");
              }
              if ($descRow = $descQuery->fetch_assoc()) {
                $deskripsi = $descRow['deskripsi'];
              }
            ?>
            <tr>
              <td class="text-center"><?= $no++ ?></td>
              <td><?= date('d M Y', strtotime($row['tanggalTransaksi'])) ?></td>
              <td class="text-center">
                <span class="badge <?= $jenis == 'Pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran' ?>">
                  <?= $jenis ?>
                </span>
              </td>
              <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
              <td><?= htmlspecialchars($deskripsi) ?></td>
              <td class="text-center">
                <a href="edit_transaksi.php?id=<?= $idTransaksi ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="hapus_transaksi.php?id=<?= $idTransaksi ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center text-muted">Tidak ada transaksi bulan ini.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
