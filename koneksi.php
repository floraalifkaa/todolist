<?php
$host = "localhost";  // Jika Anda menggunakan Laragon, localhost biasanya sudah benar
$user = "root";       // Default username untuk MySQL di Laragon adalah root
$pass = "";           // Password kosong secara default
$dbname = "todolist"; // Ganti dengan nama database Anda

// Membuat koneksi ke MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek apakah koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
