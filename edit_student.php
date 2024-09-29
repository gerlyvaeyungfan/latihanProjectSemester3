<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'tata_tertib');

// Ambil data siswa berdasarkan ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "Siswa tidak ditemukan.";
        exit();
    }
}

// Proses pembaruan data siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $class = $_POST['class'];
    $abs_number = $_POST['abs_number'];
    $username = $_POST['username'];

    $stmt = $conn->prepare("UPDATE students SET name = ?, class = ?, abs_number = ?, username = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $name, $class, $abs_number, $username, $id);
    $stmt->execute();

    header("Location: admin_dashboard.php"); // Kembali ke dashboard admin setelah pembaruan
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Siswa</title>
</head>
<body>
    <h1>Edit Siswa</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Nama Siswa" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        <input type="text" name="class" placeholder="Kelas" value="<?php echo htmlspecialchars($student['class']); ?>" required>
        <input type="number" name="abs_number" placeholder="No Absen" value="<?php echo htmlspecialchars($student['abs_number']); ?>" required>
        <input type="text" name="username" placeholder="Username Siswa" value="<?php echo htmlspecialchars($student['username']); ?>" required>
        <button type="submit">Simpan Perubahan</button>
    </form>
    <a href="admin_dashboard.php">Kembali</a>
</body>
</html>
