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

// Proses pendaftaran pengguna baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Meng-hash password
    $role = $_POST['role']; // 'admin', 'bk', atau 'siswa'

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    
    if ($stmt->execute()) {
        // Redirect ke halaman yang sama setelah berhasil menambahkan pengguna
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Pastikan tidak ada kode yang dieksekusi setelah redirect
    } else {
        echo "Terjadi kesalahan saat menambahkan pengguna: " . $conn->error;
    }
}

// Proses hapus pengguna
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Pastikan tidak ada kode yang dieksekusi setelah redirect
    } else {
        echo "Terjadi kesalahan saat menghapus pengguna: " . $conn->error;
    }
}

// Ambil semua data pengguna untuk ditampilkan
$result = $conn->query("SELECT id, username, role FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50; /* Warna hijau */
            color: white;
        }
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            color: blue;
        }
        .action-buttons a.delete {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Tambah User</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="bk">BK</option>
            <option value="siswa">Siswa</option>
        </select>
        <button type="submit">Daftar</button>
    </form>

    <h2>Daftar Pengguna</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td class="action-buttons">
                        <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a class="delete" href="?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Belum ada pengguna yang terdaftar.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
