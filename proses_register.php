<?php
include('koneksi.php'); // Koneksi ke database

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

    // Cek apakah username sudah ada
    $sql = "SELECT * FROM users WHERE username =?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username sudah terdaftar, gunakan JavaScript untuk menampilkan pesan error dan mencegah submit
        echo "<script>alert('Username sudah terdaftar!'); window.location.href='register.php';</script>";  //kembali ke register.php
        exit(); // Penting: Hentikan eksekusi skrip agar form tidak diproses lebih lanjut
    } else {
        // Masukkan data pengguna baru ke database
        $sql = "INSERT INTO users (username, password) VALUES (?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            // Registrasi berhasil, alihkan ke halaman login dengan pesan sukses menggunakan query string
            header("Location: login.php?pesan=registrasi_berhasil");
            exit(); // Pastikan untuk menghentikan eksekusi skrip setelah redirect
        } else {
            // Registrasi gagal, tampilkan pesan error dengan JavaScript dan arahkan kembali ke form registrasi
            echo "<script>alert('Gagal registrasi: ". $stmt->error. "'); window.location.href='register.php';</script>"; //kembali ke register.php
            exit(); // Penting: Hentikan eksekusi skrip
        }
    }
    $stmt->close();
    $conn->close();
}?>