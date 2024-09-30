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

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data pengguna berdasarkan ID
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Pengguna tidak ditemukan.");
}

$user = $result->fetch_assoc();

// Proses pembaruan pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $role, $id);
    
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit(); // Pastikan tidak ada kode yang dieksekusi setelah redirect
    } else {
        echo "Terjadi kesalahan saat memperbarui pengguna: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <select name="role" required>
            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="bk" <?php echo ($user['role'] === 'bk') ? 'selected' : ''; ?>>BK</option>
            <option value="siswa" <?php echo ($user['role'] === 'siswa') ? 'selected' : ''; ?>>Siswa</option>
        </select>
        <button type="submit">Perbarui</button>
    </form>
    <a href="register.php">Kembali</a> <!-- Ganti 'index.php' dengan nama file utama Anda -->
</body>
</html>
