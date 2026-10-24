<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "koneksi.php";

$pesan = "";

if (isset($_POST['login'])) {

    $username = $_POST['username'];

    $password = $_POST['password'];

    $sql = "SELECT * FROM admin
            WHERE username = '$username'
            AND password = '$password'";

    $hasil = mysqli_query($koneksi, $sql);

    if (!$hasil) {

        die("Terjadi kesalahan: " . mysqli_error($koneksi));

    }

    if (mysqli_num_rows($hasil) > 0) {

        $data = mysqli_fetch_assoc($hasil);

        $_SESSION['admin'] = $data['username'];

        $_SESSION['nama_admin'] = $data['nama'];

        header("Location: dashboard_admin.php");

        exit();

    } else {

        $pesan = "Username atau password admin salah!";

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Admin - Universitas Almarisah Madani</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="login-container">

    <div class="login-box">

        <img 
            src="logo.jpg" 
            class="login-logo" 
            alt="Logo Universitas Almarisah Madani"
        >

        <div class="login-campus">
            UNIVERSITAS ALMARISAH MADANI
        </div>

        <h2>Login Admin</h2>

        <?php if ($pesan != "") { ?>

            <p style="color:red; text-align:center; margin-bottom:15px;">
                <?php echo $pesan; ?>
            </p>

        <?php } ?>

        <form method="POST">

            <div class="form-group">

                <label>Username</label>

                <input 
                    type="text" 
                    name="username" 
                    placeholder="Masukkan username admin"
                    required
                >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input 
                    type="password" 
                    name="password" 
                    placeholder="Masukkan password admin"
                    required
                >

            </div>

            <button 
                type="submit" 
                name="login" 
                class="login-button"
            >
                Login Admin
            </button>

        </form>

        <a href="index.php" class="back-link">
            ← Kembali ke Beranda
        </a>

    </div>

</div>

</body>

</html>