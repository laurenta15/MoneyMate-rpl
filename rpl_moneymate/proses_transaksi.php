<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi DB ada

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];
    $idPengguna = $_SESSION['idPengguna']; // asumsi sudah login

    // Insert ke tabel transaksi
    $query = "INSERT INTO transaksi (tanggalTransaksi, jenisTransaksi, jumlah, idPengguna)
              VALUES ('$tanggal', '$jenis', '$jumlah', '$idPengguna')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $idTransaksi = mysqli_insert_id($conn);
        if ($jenis == 'Pemasukan') {
            mysqli_query($conn, "INSERT INTO pemasukan (idPengguna, idTransaksi, deskripsiPemasukan)
                                 VALUES ('$idPengguna', '$idTransaksi', '$deskripsi')");
        } else {
            mysqli_query($conn, "INSERT INTO pengeluaran (idPengguna, idTransaksi, deskripsiPengeluaran)
                                 VALUES ('$idPengguna', '$idTransaksi', '$deskripsi')");
        }
        header("Location: dashboard.php");
    } else {
        echo "Gagal menambahkan transaksi.";
    }
}
?>
