<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$pass = "";      // coba "" dulu, kalau root punya password ganti ke "root" atau password yang benar
$db   = "spksaw"; // coba spksaw dulu, kalau nggak ada ganti ke saw_playstore

try {
    $conn = new mysqli($host, $user, $pass, $db);
    echo "Koneksi berhasil ke database `$db` sebagai user `$user`";
} catch (Exception $e) {
    echo "ERROR DETAIL: " . $e->getMessage();
}