<?php
session_start();

$dbHost = "localhost";
$dbUsername = "ahmad";
$dbPassword = "ahmad212";
$dbName = "celengan";

// Membuat koneksi ke database
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Memeriksa koneksi
if ($conn->connect_error) {
 die("Koneksi database gagal: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
 $user_id = $_POST['user_id'];
 $password = $_POST['password'];

 $query = "SELECT * FROM registrasi WHERE user_id = '$user_id'";
 $result = $conn->query($query);

 if ($result && $result->num_rows > 0) { // Pastikan query berhasil dan ada hasil
 $row = $result->fetch_assoc();

 if (password_verify($password, $row['password'])) {
 $_SESSION['user_id'] = $row['id']; // Simpan ID pengguna, bukan user_id
 header('Location: dashboard.php');
 exit(); // Penting untuk menghentikan eksekusi script setelah redirect
 } else {
 echo "Username atau password salah!";
 }
 } else {
 echo "Username atau password salah!"; // Pesan yang lebih jelas jika user tidak ditemukan
 }
}
?>

<h2>Form Login</h2>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
 <label for="user_id">Nama Pengguna:</label><br>
 <input type="text" id="user_id" name="user_id" required><br><br>
 <label for="password">Password:</label><br>
 <input type="password" id="password" name="password" required><br><br>
 <input type="submit" name="login" value="Login">
</form>
<p>Belum punya akun? <a href="registrasi.php">Registrasi</a></p>
