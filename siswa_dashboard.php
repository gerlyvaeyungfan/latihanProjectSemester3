<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'tata_tertib');

// Periksa apakah pengguna login sebagai siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php"); // Arahkan ke halaman login jika tidak ada sesi
    exit();
}

$username = $_SESSION['username']; // Pastikan username siswa disimpan di session
$stmt = $conn->prepare("SELECT * FROM students WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $student_id = $student['id']; // Ambil student_id
    $student_name = $student['name'];
    $student_class = $student['class'];
    $abs_number = $student['abs_number'];

    // Ambil pelanggaran berdasarkan student_id dari tabel violations
    $stmt = $conn->prepare("SELECT title, description, violation_date FROM violations WHERE student_id = ?");
    $stmt->bind_param("i", $student_id); // Gunakan student_id
    $stmt->execute();
    $violations = $stmt->get_result();
} else {
    echo "Siswa tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="style.css"> <!-- Pastikan file ini ada -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
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
            background-color: #4CAF50; /* Hijau */
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5; /* Warna latar belakang saat hover */
        }
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #f44336; /* Merah untuk logout */
            font-weight: bold;
        }
        .student-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e9ffe9; /* Latar belakang untuk informasi siswa */
            border: 1px solid #4CAF50; /* Border hijau */
        }
    </style>
</head>
<body>
    <h1>Pelanggaran untuk Siswa: <?php echo htmlspecialchars($student_name); ?></h1>
    
    <!-- Menampilkan data diri siswa -->
    <div class="student-info">
        <h2>Data Diri Siswa</h2>
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($student_name); ?></p>
        <p><strong>Kelas:</strong> <?php echo htmlspecialchars($student_class); ?></p>
        <p><strong>Nomor Absensi:</strong> <?php echo htmlspecialchars($abs_number); ?></p>
    </div>

    <table border="1">
        <tr>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Tanggal</th>
        </tr>
        <?php if ($violations->num_rows > 0): ?>
            <?php while ($violation = $violations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($violation['title']); ?></td>
                    <td><?php echo htmlspecialchars($violation['description']); ?></td>
                    <td><?php echo htmlspecialchars($violation['violation_date']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Tidak ada pelanggaran yang ditemukan.</td>
            </tr>
        <?php endif; ?>
    </table>
    <a href="logout.php" class="logout-link">Logout</a>
</body>
</html>
