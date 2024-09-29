<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'tata_tertib');

// Periksa apakah pengguna memiliki hak akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Hapus pelanggaran jika ID diberikan
if (isset($_GET['id'])) {
    $violation_id = $_GET['id'];

    // Hapus data pelanggaran dari database
    $stmt = $conn->prepare("DELETE FROM violations WHERE id = ?");
    $stmt->bind_param("i", $violation_id);
    $stmt->execute();

    echo "Pelanggaran berhasil dihapus!";
    header("Location: manajemen_pelanggaran.php"); // Redirect ke halaman manajemen pelanggaran setelah menghapus
    exit();
} else {
    echo "ID pelanggaran tidak disediakan!";
    exit();
}
?>
