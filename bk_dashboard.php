<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bk') {
    header("Location: login.php");
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'tata_tertib');

// Proses penambahan pelanggaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari input
    $title = $_POST['title'];
    $description = $_POST['description'];
    $violation_date = $_POST['violation_date'];
    $student_id = $_POST['student_id']; // Ambil student_id dari form

    // Menyimpan pelanggaran ke dalam database
    $stmt = $conn->prepare("INSERT INTO violations (title, description, violation_date, student_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $violation_date, $student_id);
    $stmt->execute();

    echo "Pelanggaran berhasil ditambahkan.";
}

// Proses pengeditan pelanggaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $violation_id = $_POST['violation_id'];
    $student_id = $_POST['student_id']; // Menggunakan student_id
    $title = $_POST['title'];
    $description = $_POST['description'];
    $violation_date = $_POST['violation_date'];

    // Update data pelanggaran
    $stmt = $conn->prepare("UPDATE violations SET student_id = ?, title = ?, description = ?, violation_date = ? WHERE id = ?");
    $stmt->bind_param("issii", $student_id, $title, $description, $violation_date, $violation_id);
    $stmt->execute();

    echo "<p>Pelanggaran berhasil diperbarui.</p>";
}

// Hapus pelanggaran
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $violation_id = $_GET['id'];
    
    // Hapus data pelanggaran dari database
    $stmt = $conn->prepare("DELETE FROM violations WHERE id = ?");
    $stmt->bind_param("i", $violation_id);
    $stmt->execute();

    echo "<p>Pelanggaran berhasil dihapus!</p>";
}

// Ambil semua data pelanggaran untuk ditampilkan
$violations = $conn->query("SELECT * FROM violations");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pelanggaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
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
            background-color: #4CAF50; /* Warna hijau */
            color: white;
        }
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #f44336; /* Merah untuk logout */
            font-weight: bold;
        }
    </style>
</head>
<body>
<h1>Tambah Pelanggaran</h1>
<form method="POST">
    <input type="hidden" name="action" value="add">
    <table style="width: 100%; margin: left; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Pilih Siswa</th>
                <th>Judul Pelanggaran</th>
                <th>Deskripsi Pelanggaran</th>
                <th>Tanggal Pelanggaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select name="student_id" id="student_id" required style="width: 100%;">
                        <option value="">Pilih Siswa</option>
                        <?php
                        // Ambil data siswa dengan id dan nama
                        $students = $conn->query("SELECT id, name FROM students");
                        while ($student = $students->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($student['id']) . "'>" . htmlspecialchars($student['name']) . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="title" id="title" placeholder="Judul Pelanggaran" required style="width: 100%;">
                </td>
                <td>
                    <textarea name="description" id="description" rows="1" placeholder="Deskripsi Pelanggaran" required style="width: 100%;"></textarea>
                </td>
                <td>
                    <input type="date" name="violation_date" id="violation_date" required style="width: 100%;">
                </td>
                <td colspan="4" style="text-align: center;">
                    <button type="submit" style="background-color: green; color: white; padding: 10px 15px; border: none; border-radius: 5px;">Tambah</button>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<h2>Daftar Pelanggaran</h2>
<table>
    <tr>
        <th>Nama Siswa</th>
        <th>Judul Pelanggaran</th>
        <th>Deskripsi</th>
        <th>Tanggal Pelanggaran</th>
        <th>Aksi</th>
    </tr>
    <?php if ($violations->num_rows > 0): ?>
        <?php while ($violation = $violations->fetch_assoc()): ?>
            <?php
            // Ambil nama siswa berdasarkan student_id
            $student_stmt = $conn->prepare("SELECT name FROM students WHERE id = ?");
            $student_stmt->bind_param("i", $violation['student_id']);
            $student_stmt->execute();
            $student_result = $student_stmt->get_result();
            $student_data = $student_result->fetch_assoc();
            $student_name = $student_data['name'];
            ?>
            <tr>
                <td><?php echo htmlspecialchars($student_name); ?></td>
                <td><?php echo htmlspecialchars($violation['title']); ?></td>
                <td><?php echo htmlspecialchars($violation['description']); ?></td>
                <td><?php echo htmlspecialchars($violation['violation_date']); ?></td>
                <td>
                    <a href="edit_pelanggaran.php?id=<?php echo $violation['id']; ?>">Edit</a> |
                    <a href="?action=delete&id=<?php echo $violation['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggaran ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">Belum ada pelanggaran yang ditambahkan.</td>
        </tr>
    <?php endif; ?>
</table>
<a href="logout.php" class="logout-link">Logout</a>
</body>
</html>
