<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'tata_tertib');

// Ambil data pelanggaran berdasarkan ID
if (isset($_GET['id'])) {
    $violation_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM violations WHERE id = ?");
    $stmt->bind_param("i", $violation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $violation = $result->fetch_assoc();
    } else {
        echo "Pelanggaran tidak ditemukan!";
        exit();
    }

    // Proses update jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $student_name = $_POST['student_name'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $violation_date = $_POST['violation_date'];

        $stmt = $conn->prepare("UPDATE violations SET student_name = ?, title = ?, description = ?, violation_date = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $student_name, $title, $description, $violation_date, $violation_id);
        $stmt->execute();

        echo "Pelanggaran berhasil diperbarui!";
        header("Location: bk_dashboard.php");
        exit();
    }
} else {
    echo "ID pelanggaran tidak disediakan!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pelanggaran</title>
</head>
<body>
    <h1>Edit Pelanggaran</h1>
    <form method="POST">
        <label>Nama Siswa:</label>
        <input type="text" name="student_name" value="<?php echo htmlspecialchars($violation['student_name']); ?>" readonly required>
        
        <label>Judul Pelanggaran:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($violation['title']); ?>" required>
        
        <label>Deskripsi Pelanggaran:</label>
        <textarea name="description" required><?php echo htmlspecialchars($violation['description']); ?></textarea>
        
        <label>Tanggal Pelanggaran:</label>
        <input type="date" name="violation_date" value="<?php echo htmlspecialchars($violation['violation_date']); ?>" required>
        
        <button type="submit">Simpan Perubahan</button>
    </form>
    <a href="bk_dashboard.php">Kembali</a>
</body>
</html>
