<?php
session_start();

// Pastikan pengguna sudah login
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

// Ambil ID pengguna dari sesi
$user_id = $_SESSION['user_id'];

// Periksa apakah akun masih ada
$cek_user = $conn->query("SELECT id FROM registrasi WHERE id='$user_id'");

if ($cek_user->num_rows > 0) {
    // Hapus akun dari database
    $conn->query("DELETE FROM registrasi WHERE id='$user_id'");

    // Hancurkan semua sesi dan cookie
    $_SESSION = array(); // Hapus semua variabel sesi

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy(); // Hancurkan sesi

    // Redirect ke halaman login.php
    header("Location: login.php");
    exit();
} else {
    echo "Akun tidak ditemukan!";
}

$conn->close();
?>
