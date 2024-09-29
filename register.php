<?php
$conn = new mysqli('localhost', 'root', '', 'tata_tertib');

// Proses pendaftaran pengguna baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Meng-hash password
    $role = $_POST['role']; // 'admin', 'bk', atau 'siswa'

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    
    if ($stmt->execute()) {
        echo "Pengguna berhasil ditambahkan.";
    } else {
        echo "Terjadi kesalahan saat menambahkan pengguna: " . $conn->error;
    }
}

// Ambil semua data pengguna untuk ditampilkan
$result = $conn->query("SELECT username, role FROM users");
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
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">Belum ada pengguna yang terdaftar.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
