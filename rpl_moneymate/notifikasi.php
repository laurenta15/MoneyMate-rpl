<?php
// Ambil status notifikasi pengguna
$cekNotifikasi = $conn->query("SELECT notifikasiAktif FROM pengguna WHERE idPengguna = $id")->fetch_assoc();
$notifikasiAktif = $cekNotifikasi['notifikasiAktif'];

$notifikasi = [];

if ($notifikasiAktif) {
  $bulanIni = date('m');
  $tahunIni = date('Y');

  $pemasukanBulanIni = $conn->query("
    SELECT IFNULL(SUM(jumlah), 0) AS total 
    FROM transaksi 
    WHERE jenisTransaksi = 'Pemasukan' AND idPengguna = $id 
      AND MONTH(tanggalTransaksi) = $bulanIni AND YEAR(tanggalTransaksi) = $tahunIni
  ")->fetch_assoc()['total'];

  $pengeluaranBulanIni = $conn->query("
    SELECT IFNULL(SUM(jumlah), 0) AS total 
    FROM transaksi 
    WHERE jenisTransaksi = 'Pengeluaran' AND idPengguna = $id 
      AND MONTH(tanggalTransaksi) = $bulanIni AND YEAR(tanggalTransaksi) = $tahunIni
  ")->fetch_assoc()['total'];

  // Notifikasi kondisi keuangan
  if ($pengeluaranBulanIni > $pemasukanBulanIni) {
    $notifikasi[] = "⚠️ Pengeluaran bulan ini lebih besar dari pemasukan.";
  }

  if ($saldo < 0) {
    $notifikasi[] = "⚠️ Saldo Anda negatif. Pengeluaran melebihi pemasukan.";
  } elseif ($saldo == 0) {
    $notifikasi[] = "⚠️ Saldo Anda saat ini kosong. Periksa kembali keuangan Anda.";
  } elseif ($saldo <= 50000) {
    $notifikasi[] = "⚠️ Saldo Anda hampir habis. Saatnya berhemat.";
  }

  if ($pemasukanBulanIni == 0 && $pengeluaranBulanIni == 0) {
    $notifikasi[] = "📭 Belum ada transaksi bulan ini. Yuk mulai catat pengeluaranmu!";
  }

  if ($pemasukanBulanIni > 0 && $pengeluaranBulanIni < $pemasukanBulanIni && $saldo > 50000) {
    $notifikasi[] = "🎉 Keuangan Anda sehat bulan ini. Pertahankan!";
  }
}
?>
