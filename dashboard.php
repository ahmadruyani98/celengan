
<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "Session User ID: " . $_SESSION['user_id'];

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username_db = "ahmad";
$password_db = "ahmad212";
$dbname = "celengan";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil nama pengguna dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT user_id FROM registrasi WHERE id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nama_pengguna = $row['user_id']; // Mengambil nama pengguna
} else {
    $nama_pengguna = "Pengguna Tidak Ditemukan";
}

// Pastikan tabel transaksi sudah ada dan memiliki kolom user_id
$conn->query("CREATE TABLE IF NOT EXISTS transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(255) NOT NULL,
    tipe_transaksi VARCHAR(255),
    pemasukan DECIMAL(10,2),
    pengeluaran DECIMAL(10,2),
    hari VARCHAR(255),
    tanggal INT,
    bulan INT,
    tahun INT,
    keterangan VARCHAR(255)
)");

// Inisialisasi variabel saldo, total pemasukan, dan total pengeluaran
$saldo = 0;
$total_pemasukan = 0;
$total_pengeluaran = 0;

// Proses form tambah transaksi jika disubmit
if (isset($_POST['add_transaksi'])) {
    $tipe_transaksi = $_POST['tipe_transaksi'];
    $pemasukan = $_POST['pemasukan'];
    $pengeluaran = $_POST['pengeluaran'];
    $keterangan = $_POST['keterangan'];

    // Ambil tanggal saat ini
    $hari = date("l"); // Nama hari (e.g., Monday)
    $tanggal = date("d"); // Tanggal (e.g., 21)
    $bulan = date("m"); // Bulan (e.g., 05)
    $tahun = date("Y"); // Tahun (e.g., 2025)

    // Query untuk menyimpan transaksi baru
    // Pastikan user_id yang disimpan sesuai dengan user yang login
    $sql_insert = "INSERT INTO transaksi (user_id, tipe_transaksi, pemasukan, pengeluaran, hari, tanggal, bulan, tahun, keterangan)
                   VALUES ('$nama_pengguna', '$tipe_transaksi', '$pemasukan', '$pengeluaran', '$hari', '$tanggal', '$bulan', '$tahun', '$keterangan')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>alert('Transaksi berhasil ditambahkan!');</script>";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

// Proses hapus transaksi
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Query untuk menghapus transaksi
    // Pastikan hanya user yang login yang bisa menghapus transaksinya sendiri
    $sql_delete = "DELETE FROM transaksi WHERE id = '$delete_id' AND user_id = '$nama_pengguna'";

    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Transaksi berhasil dihapus!');</script>";
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
}

// Proses logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Query untuk mengambil semua transaksi
// Pastikan hanya menampilkan transaksi milik user yang login
$sql = "SELECT * FROM transaksi WHERE user_id = '$nama_pengguna'";
$result = $conn->query($sql);

// Hitung saldo, total pemasukan, dan total pengeluaran
// Pastikan perhitungan hanya berdasarkan transaksi milik user yang login
$sql = "SELECT * FROM transaksi WHERE user_id = '$nama_pengguna'";
$result = $conn->query($sql);

$total_pemasukan = 0;
$total_pengeluaran = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $total_pemasukan += $row["pemasukan"];
        $total_pengeluaran += $row["pengeluaran"];
    }
}

$saldo =...
$total_pemasukan - $total_pengeluaran;

/* Query untuk mengambil semua transaksi (untuk ditampilkan di tabel)
Pastikan hanya menampilkan transaksi milik user yang login */
$sql = "SELECT * FROM transaksi WHERE user_id = '$nama_pengguna'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Celengan Digital</title>
    <style>
        body {
            margin: 0; /* Menghilangkan margin default body */
            font-family: Arial, sans-serif;
        }
        .hamburger-menu {
            cursor: pointer;
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10; /* Memastikan menu hamburger selalu di atas konten lain */
        }
        .menu-content {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            background: white;
            border: 1px solid #ccc;
            padding: 10px;
            z-index: 5; /* Memastikan menu berada di atas konten utama */
        }
        .saldo-table {
            display: inline-block;
            vertical-align: top;
            margin-right: 20px;
        }
    </style>
    <script>
        function toggleMenu() {
            var menu = document.getElementById("menu-content");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }
    </script>
</head>
<body>
    <div class="hamburger-menu" onclick="toggleMenu()">
        ☰ Menu
    </div>
    <div class="menu-content" id="menu-content">
        <form method="post">
            <input type="submit" name="logout" value="Logout">
        </form>
        <form method="post" action="hapus_akun.php">
            <input type="submit" name="hapus_akun" value="Hapus Akun">
        </form>
    </div>

    <h1>Celengan Digital</h1>

    <h3>Selamat datang, <?php echo htmlspecialchars($nama_pengguna); ?>!</h3>

    <div class="saldo-table">
        <h2>Saldo</h2>
        <table>
            <tr>
                <td>Saldo:</td>
                <td>Rp <?php echo number_format($saldo, 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Total Pemasukan:</td>
                <td>Rp <?php echo number_format($total_pemasukan, 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Total Pengeluaran:</td>
                <td>Rp <?php echo number_format($total_pengeluaran, 2, ',', '.'); ?></td>
            </tr>
        </table>
    </div>

    <h2>Tambah Transaksi</h2>
    <form method="post">
        <label for="tipe_transaksi">Tipe Transaksi:</label>
        <select name="tipe_transaksi" id="tipe_transaksi">
            <option value="Pemasukan">Pemasukan</option>
            <option value="Pengeluaran">Pengeluaran</option>
        </select><br><br>

        <label for="pemasukan">Pemasukan:</label>
        <input type="number" name="pemasukan" id="pemasukan" value="0"><br><br>

        <label for="pengeluaran">Pengeluaran:</label>
        <input type="number" name="pengeluaran" id="pengeluaran" value="0"><br><br>

        <label for="keterangan">Keterangan:</label>
        <input type="text" name="keterangan" id="keterangan"><br><br>

        <input type="submit" name="add_transaksi" value="Tambah Transaksi">
    </form>

    <h2>Riwayat Transaksi</h2>
    <table>
        <thead>
            <tr>
                <th>Tipe</th>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
                <th>Hari</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row["tipe_transaksi"]."</td>";
                    echo "<td>Rp ".number_format($row["pemasukan"], 2, ',', '.')."</td>";
                    echo "<td>Rp ".number_format($row["pengeluaran"], 2, ',', '.')."</td>";
                    echo...
"<td>".$row["hari"]."</td>";
                    echo "<td>".$row["tanggal"]."/".$row["bulan"]."/".$row["tahun"]."</td>";
                    echo "<td>".$row["keterangan"]."</td>";
                    echo "<td><a href='?delete_id=".$row["id"]."'>Hapus</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Tidak ada transaksi.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
