<?php

session_start();
include "koneksi.php";

$pesan = "";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM mahasiswa
            WHERE username = '$username'
            AND password = '$password'";

    $hasil = mysqli_query($koneksi, $sql);

    if (!$hasil) {
        die("Terjadi kesalahan: " . mysqli_error($koneksi));
    }

    if (mysqli_num_rows($hasil) > 0) {

        $data = mysqli_fetch_assoc($hasil);

        $_SESSION['nim'] = $data['nim'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['prodi'] = $data['prodi'];

        header("Location: dashboard.php");
        exit();

    } else {

        $pesan = "Username atau password salah!";

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Mahasiswa - Universitas Almarisah Madani</title>

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

        <h2>
            Login Mahasiswa
        </h2>

        <?php if ($pesan != "") { ?>

            <p style="color:red; text-align:center; margin-bottom:15px;">
                <?php echo $pesan; ?>
            </p>

        <?php } ?>

        <form method="POST">

            <div class="form-group">

                <label>
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    placeholder="Masukkan username"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

            </div>


            <button
                type="submit"
                name="login"
                class="login-button"
            >
                Login Mahasiswa
            </button>

        </form>


        <a href="index.php" class="back-link">
            ← Kembali ke Beranda
        </a>

    </div>

</div>

</body>

</html>