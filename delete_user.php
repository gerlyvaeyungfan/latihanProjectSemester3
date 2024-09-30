<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'tata_tertib');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses hapus pengguna
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: index.php"); // Ganti 'index.php' dengan nama file utama Anda
        exit(); // Pastikan tidak ada kode yang dieksekusi setelah redirect
    } else {
        echo "Terjadi kesalahan saat menghapus pengguna: " . $conn->error;
    }
} else {
    echo "ID pengguna tidak ditentukan.";
}
?>
