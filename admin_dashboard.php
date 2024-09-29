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

// Proses penambahan siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $class = $conn->real_escape_string($_POST['class']);
    $abs_number = (int)$_POST['abs_number']; // Konversi ke integer
    $username = $conn->real_escape_string($_POST['username']);

    // Menambahkan siswa dengan kolom username
    $stmt = $conn->prepare("INSERT INTO students (name, class, abs_number, username) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $class, $abs_number, $username);
    
    if ($stmt->execute()) {
        echo "<p>Siswa berhasil ditambahkan.</p>";
    } else {
        echo "<p>Gagal menambahkan siswa: " . $conn->error . "</p>";
    }
}


// Proses hapus siswa
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete']; // Konversi ke integer
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<p>Siswa berhasil dihapus.</p>";
    } else {
        echo "<p>Gagal menghapus siswa: " . $conn->error . "</p>";
    }
}

// Ambil semua data siswa untuk ditampilkan
$result = $conn->query("SELECT id, name, class, abs_number, username FROM students");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
        nav {
            margin-bottom: 20px;
        }
        nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #4CAF50; /* Warna hijau */
        }
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #f44336; /* Merah untuk logout */
            font-weight: bold;
        }
        .action-links a {
            margin-right: 10px;
            text-decoration: none;
            color: #2196F3; /* Warna biru */
        }
    </style>
</head>
<body>
    <nav>
        <a href="register.php">Tambah User</a>
    </nav>
    
    <h1>Tambah Siswa</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Nama Siswa" required>
        <input type="text" name="class" placeholder="Kelas" required>
        <input type="number" name="abs_number" placeholder="No Absen" required>
        <input type="text" name="username" placeholder="Username Siswa" required>
        <button type="submit">Tambah</button>
    </form>

    <h2>Daftar Siswa</h2>
    <table>
        <tr>
            <th>Nama</th>
            <th>Kelas</th>
            <th>No Absen</th>
            <th>Username</th>
            <th>Aksi</th> <!-- Kolom Aksi -->
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['class']); ?></td>
                    <td><?php echo htmlspecialchars($row['abs_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td class="action-links">
                        <a href="edit_student.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Anda yakin ingin menghapus siswa ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Belum ada siswa yang ditambahkan.</td>
            </tr>
        <?php endif; ?>
    </table>
    <a href="logout.php" class="logout-link">Logout</a>
</body>
</html>
