<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi</title>
    <style>
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php
    // Informasi koneksi ke database
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

    // Query untuk membuat tabel registrasi jika belum ada
    $createTableSQL = "CREATE TABLE IF NOT EXISTS registrasi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        user_id VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL,
        address TEXT,
        gender ENUM('Laki-laki', 'Perempuan'),
        date_of_birth DATE,
        phone_number VARCHAR(20),
        password VARCHAR(255) NOT NULL
    )";

    if ($conn->query($createTableSQL) === TRUE) {
        echo "<p>Tabel 'registrasi' berhasil dibuat atau sudah ada.</p>";

        // Cek apakah sudah disubmit data dari form
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Menangkap data dari form
            $full_name = $_POST['full_name'];
            $user_id = $_POST['user_id']; // Menggunakan user_id sebagai nama pengguna
            $email = $_POST['email'];
            $address = $_POST['address'];
            $gender = $_POST['gender'];
            $date_of_birth = $_POST['date_of_birth'];
            $phone_number = $_POST['phone_number'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Query untuk menyimpan data ke tabel registrasi
            $insertSQL = "INSERT INTO registrasi (full_name, user_id, email, address, gender, date_of_birth, phone_number, password)
                          VALUES ('$full_name', '$user_id', '$email', '$address', '$gender', '$date_of_birth', '$phone_number', '$password')";

            if ($conn->query($insertSQL) === TRUE) {
                // Redirect ke halaman login.php setelah registrasi berhasil
                echo '<script>window.location.href = "login.php";</script>';
                exit;
            } else {
                echo "Error: " . $insertSQL . "<br>" . $conn->error;
            }
        }

        // Form untuk input data registrasi
        echo '
        <h2>Form Registrasi</h2>
        <form method="POST" action="' . $_SERVER['PHP_SELF'] . '">
            <label for="full_name">Nama Lengkap:</label><br>
            <input type="text" id="full_name" name="full_name" required><br><br>
            
            <label for="user_id">Nama Pengguna:</label><br>
            <input type="text" id="user_id" name="user_id" required><br><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="address">Alamat:</label><br>
            <textarea id="address" name="address"></textarea><br><br>
            
            <label for="gender">Jenis Kelamin:</label><br>
            <input type="radio" id="laki-laki" name="gender" value="Laki-laki" required>
            <label for="laki-laki">Laki-laki</label><br>
            <input type="radio" id="perempuan" name="gender" value="Perempuan">
            <label for="perempuan">Perempuan</label><br><br>
            
            <label for="date_of_birth">Tanggal Lahir:</label><br>
            <input type="date" id="date_of_birth" name="date_of_birth" required><br><br>
            
            <label for="phone_number">Nomor Telepon:</label><br>
            <input type="text" id="phone_number" name="phone_number"><br><br>
            
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            
            <input type="submit" class="button" value="Register">
        </form>';
        
        // Tautan untuk login jika sudah punya akun
        echo '<p>Sudah punya akun? <a href="login.php">Login</a></p>';
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // Menutup koneksi
    $conn->close();
    ?>
    <p>sudah punya akun?</p>
    <a href="login.php">MASUK</a>
</body>
</html>
