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

// Membuat tabel registrasi jika belum ada
$query = "CREATE TABLE IF NOT EXISTS registrasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    user_id VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
)";

$conn->query($query);

if (isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Memeriksa apakah user ID sudah ada
    $query = "SELECT * FROM registrasi WHERE user_id = '$user_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "User ID sudah digunakan, silakan pilih user ID lain.";
    } else {
        $query = "INSERT INTO registrasi (full_name, user_id, email, password) VALUES ('$full_name', '$user_id', '$email', '$password')";
        if ($conn->query($query) === TRUE) {
            echo "Registrasi berhasil!";
            header('Location: login.php');
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
    }
}
?>

<h2>Form Registrasi</h2>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="full_name">Nama Lengkap:</label><br>
    <input type="text" id="full_name" name="full_name" required><br><br>
    <label for="user_id">Nama Pengguna:</label><br>
    <input type="text" id="user_id" name="user_id" required><br><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" name="register" value="Register">
</form>
<p>Sudah punya akun? <a href="login.php">Login</a></p>
