<!-- hapus_transaksi.php -->
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

// Cek jenis transaksi
$query = "SELECT jenisTransaksi FROM transaksi WHERE idTransaksi = $id AND idPengguna = $idPengguna";
$result = $conn->query($query);
if (!$result || $result->num_rows === 0) {
    echo "Transaksi tidak ditemukan.";
    exit();
}
$jenis = $result->fetch_assoc()['jenisTransaksi'];

// Hapus deskripsi terlebih dahulu
if ($jenis == 'Pemasukan') {
    $conn->query("DELETE FROM pemasukan WHERE idTransaksi = $id");
} else {
    $conn->query("DELETE FROM pengeluaran WHERE idTransaksi = $id");
}
// Hapus transaksi utama
$conn->query("DELETE FROM transaksi WHERE idTransaksi = $id");

header("Location: riwayat_transaksi.php");
?>
