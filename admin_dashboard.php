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
            width: 50%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50; /* Warna hijau */
            color: white;
            padding: 10px;
        }
        td {
            padding: 10px;
        }
        nav {
            margin-bottom: 20px;
            margin-left: 2;
        }
        nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #4CAF50; /* Warna hijau */
        }
        .logout-link {
            display: block;
            text-align: left;
            margin-top: 20px;
            text-decoration: none;
            color: #f44336; /* Merah untuk logout */
            font-weight: bold;
        }
        .action-links a {
            margin-left: 10px;
            text-decoration: none;
            color: #2196F3; /* Warna biru */
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            display: inline;
            margin-right: 15px;
        }

        nav ul li a {
            text-decoration: none;
            color: #4CAF50; /* Warna hijau */
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="register.php">Tambah User</a></li>
        </ul>
    </nav>
    
    <h1>Tambah Siswa</h1>
    <form method="POST">
        <table>
            <tr>
                <th>Informasi</th>
                <th>Data</th>
            </tr>
            <tr>
                <td><label for="name">Nama Siswa:</label></td>
                <td><input type="text" id="name" name="name" placeholder="Nama Siswa" required></td>
            </tr>
            <tr>
                <td><label for="class">Kelas:</label></td>
                <td><input type="text" id="class" name="class" placeholder="Kelas" required></td>
            </tr>
            <tr>
                <td><label for="abs_number">No Absen:</label></td>
                <td><input type="number" id="abs_number" name="abs_number" placeholder="No Absen" required></td>
            </tr>
            <tr>
                <td><label for="username">Username Siswa:</label></td>
                <td><input type="text" id="username" name="username" placeholder="Username Siswa" required></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button type="submit">Tambah</button>
                </td>
            </tr>
        </table>
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
