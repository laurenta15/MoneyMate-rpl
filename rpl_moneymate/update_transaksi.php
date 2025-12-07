<!-- update_transaksi.php -->
<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$idPengguna = $_SESSION['user']['idPengguna'];
$id = $_POST['id'];
$tanggal = $_POST['tanggal'];
$jumlah = $_POST['jumlah'];
$deskripsi = $_POST['deskripsi'];

// Cek jenis transaksi
$query = "SELECT jenisTransaksi FROM transaksi WHERE idTransaksi = $id AND idPengguna = $idPengguna";
$result = $conn->query($query);
if (!$result || $result->num_rows === 0) {
    echo "Transaksi tidak ditemukan.";
    exit();
}
$jenis = $result->fetch_assoc()['jenisTransaksi'];

// Update transaksi utama
$conn->query("UPDATE transaksi SET tanggalTransaksi = '$tanggal', jumlah = '$jumlah' WHERE idTransaksi = $id");

// Update deskripsi
if ($jenis == 'Pemasukan') {
    $conn->query("UPDATE pemasukan SET deskripsiPemasukan = '$deskripsi' WHERE idTransaksi = $id");
} else {
    $conn->query("UPDATE pengeluaran SET deskripsiPengeluaran = '$deskripsi' WHERE idTransaksi = $id");
}

header("Location: riwayat_transaksi.php");
?>
