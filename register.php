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

// Ambil data pengguna berdasarkan role
$adminUsers = $conn->query("SELECT id, username FROM users WHERE role = 'admin'");
$bkUsers = $conn->query("SELECT id, username FROM users WHERE role = 'bk'");
$siswaUsers = $conn->query("SELECT id, username FROM users WHERE role = 'siswa'");
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Tambah User</title>
        <style>
            table {
                width: 40%;
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
            .role-title {
                margin-top: 30px;
                font-size: 24px;
                color: #333;
            }
        </style>
    </head>
    <body>
        <h1>Tambah User</h1>
        <form method="POST" id="userForm" style="max-width: 400px; margin-top: 20px;">
            <div style="margin-bottom: 15px;">
                <label for="username" style="display: block; margin-bottom: 5px;">Username</label>
                <input type="text" name="username" id="username" placeholder="Username" style="width: 100%; padding: 8px; box-sizing: border-box;">
                <small id="usernameError" style="color: red; display: none;">Username wajib diisi!</small>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" style="width: 100%; padding: 8px; box-sizing: border-box;">
                <small id="passwordError" style="color: red; display: none;">Password wajib diisi!</small>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="role" style="display: block; margin-bottom: 5px;">Role</label>
                <select name="role" id="role" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="admin">Admin</option>
                    <option value="bk">BK</option>
                    <option value="siswa">Siswa</option>
                </select>
            </div>

            <div>
                <button type="submit" style="width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
                    Daftar
                </button>
            </div>
        </form>

        <script>
        document.getElementById("userForm").addEventListener("submit", function(event) {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var usernameError = document.getElementById("usernameError");
            var passwordError = document.getElementById("passwordError");

            var valid = true;

            // Cek Username
            if (username.trim() === "") {
                usernameError.style.display = "block"; // Tampilkan pesan error username
                valid = false;
            } else {
                usernameError.style.display = "none"; // Sembunyikan pesan error username
            }

            // Cek Password
            if (password.trim() === "") {
                passwordError.style.display = "block"; // Tampilkan pesan error password
                valid = false;
            } else {
                passwordError.style.display = "none"; // Sembunyikan pesan error password
            }

            if (!valid) {
                event.preventDefault(); // Mencegah form dikirim jika ada error
            }
        });
        </script>

        <!-- Tabel Pengguna Admin -->
        <div class="role-title">Admin</div>
        <table>
            <tr>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
            <?php if ($adminUsers->num_rows > 0): ?>
                <?php while ($row = $adminUsers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="action-buttons">
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a class="delete" href="?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Tidak ada pengguna admin yang terdaftar.</td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- Tabel Pengguna BK -->
        <div class="role-title">BK</div>
        <table>
            <tr>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
            <?php if ($bkUsers->num_rows > 0): ?>
                <?php while ($row = $bkUsers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="action-buttons">
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a class="delete" href="?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Tidak ada pengguna BK yang terdaftar.</td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- Tabel Pengguna Siswa -->
        <div class="role-title">Siswa</div>
        <table>
            <tr>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
            <?php if ($siswaUsers->num_rows > 0): ?>
                <?php while ($row = $siswaUsers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="action-buttons">
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a class="delete" href="?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Tidak ada pengguna siswa yang terdaftar.</td>
                </tr>
            <?php endif; ?>
        </table>
    </body>
</html>