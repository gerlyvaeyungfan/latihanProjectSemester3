<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'tata_tertib');

// Ambil data pelanggaran berdasarkan ID
if (isset($_GET['id'])) {
    $violation_id = $_GET['id'];

    // Mengambil pelanggaran dan nama siswa yang terkait
    $stmt = $conn->prepare("
        SELECT violations.*, students.name as student_name 
        FROM violations 
        JOIN students ON violations.student_id = students.id 
        WHERE violations.id = ?
    ");
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
        $title = $_POST['title'];
        $description = $_POST['description'];
        $violation_date = $_POST['violation_date'];

        // Update data pelanggaran
        $stmt = $conn->prepare("UPDATE violations SET title = ?, description = ?, violation_date = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $description, $violation_date, $violation_id);
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
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
        }

        td {
            padding: 10px;
        }

        button:hover {
            background-color: #45a049;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #4CAF50;
        }
    </style>

</head>
<body>
    <h1>Edit Pelanggaran</h1>
    <form method="POST">
        <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 50%;">
            <tr>
                <th>Informasi</th>
                <th>Data</th>
            </tr>
            <tr>
                <td><label>Nama Siswa:</label></td>
                <td>
                    <input type="text" name="student_name" value="<?php echo htmlspecialchars($violation['student_name'] ?? ''); ?>" readonly required>
                </td>
            </tr>
            <tr>
                <td><label>Judul Pelanggaran:</label></td>
                <td>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($violation['title'] ?? ''); ?>" required>
                </td>
            </tr>
            <tr>
                <td><label>Deskripsi Pelanggaran:</label></td>
                <td>
                    <textarea name="description" required><?php echo htmlspecialchars($violation['description'] ?? ''); ?></textarea>
                </td>
            </tr>
            <tr>
                <td><label>Tanggal Pelanggaran:</label></td>
                <td>
                    <input type="date" name="violation_date" value="<?php echo htmlspecialchars($violation['violation_date'] ?? ''); ?>" required>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Simpan Perubahan</button>
                </td>
            </tr>
        </table>
    </form>
    <a href="bk_dashboard.php" style="display: inline-block; margin-top: 20px; text-decoration: none; color: #4CAF50;">Kembali</a>
</body>


</html>
