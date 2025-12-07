<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$id = $_SESSION['user']['idPengguna'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$noTelp = $_POST['noTelp'];

$namaBaru = $_SESSION['user']['fotoProfil']; // default foto lama

// Jika ada file foto baru yang diunggah
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
  $namaFile = $_FILES['foto']['name'];
  $tmp = $_FILES['foto']['tmp_name'];
  $ekstensi = pathinfo($namaFile, PATHINFO_EXTENSION);
  $namaBaru = 'foto_' . time() . '.' . $ekstensi;
  $path = 'uploads/' . $namaBaru;

  // Buat folder uploads jika belum ada
  if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
  }

  // Pindahkan file ke folder uploads
  if (!move_uploaded_file($tmp, $path)) {
    echo "Gagal mengunggah file gambar.";
    exit;
  }
}

// Update data pengguna di database
$stmt = $conn->prepare("UPDATE pengguna SET namaPengguna=?, email=?, noTelp=?, fotoProfil=? WHERE idPengguna=?");
$stmt->bind_param("ssssi", $nama, $email, $noTelp, $namaBaru, $id);

if ($stmt->execute()) {
  // Update data session agar profil langsung berubah
  $_SESSION['user']['namaPengguna'] = $nama;
  $_SESSION['user']['email'] = $email;
  $_SESSION['user']['noTelp'] = $noTelp;
  $_SESSION['user']['fotoProfil'] = $namaBaru;

  header("Location: profil.php");
  exit;
} else {
  echo "Gagal menyimpan perubahan.";
}
?>
