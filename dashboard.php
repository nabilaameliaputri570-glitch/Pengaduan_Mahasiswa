<?php

session_start();

if (!isset($_SESSION['nim'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Mahasiswa - Universitas Almarisah Madani</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="header">

        <div class="header-left">

            <img src="logo.jpg" class="header-logo" alt="Logo Kampus">

            <div class="header-title">
                UNIVERSITAS ALMARISAH MADANI
            </div>

        </div>

        <div>
            <a href="logout.php" style="color:white;">
                Logout
            </a>
        </div>

    </div>


    <div class="dashboard-layout">


        <div class="sidebar">

            <div class="sidebar-title">
                MENU MAHASISWA
            </div>

            <a href="dashboard.php" class="active">
                🏠 Dashboard
            </a>

            <a href="pengaduan.php">
                📝 Buat Pengaduan
            </a>

            <a href="data_pengaduan.php">
                📋 Riwayat Pengaduan
            </a>

            <a href="logout.php">
                🚪 Logout
            </a>

        </div>


        <div class="main-content">


            <div class="welcome-card">

                <h2>
                    Selamat Datang, <?php echo $_SESSION['nama']; ?>!
                </h2>

                <p>
                    Selamat datang di Aplikasi Pengaduan Mahasiswa
                    Universitas Almarisah Madani.
                </p>

            </div>


            <div class="info-cards">


                <div class="info-card">

                    <h4>Nama Mahasiswa</h4>

                    <div class="number" style="font-size:18px;">
                        <?php echo $_SESSION['nama']; ?>
                    </div>

                </div>


                <div class="info-card">

                    <h4>NIM</h4>

                    <div class="number" style="font-size:22px;">
                        <?php echo $_SESSION['nim']; ?>
                    </div>

                </div>


                <div class="info-card">

                    <h4>Program Studi</h4>

                    <div class="number" style="font-size:18px;">
                        <?php echo $_SESSION['prodi']; ?>
                    </div>

                </div>


                <div class="info-card">

                    <h4>Status</h4>

                    <div class="number" style="font-size:18px;">
                        Mahasiswa
                    </div>

                </div>


            </div>


            <div class="card">

                <h2 class="title">
                    Menu Pengaduan
                </h2>

                <p style="margin-bottom:20px;">
                    Silakan pilih menu yang ingin Anda gunakan.
                </p>


                <a href="pengaduan.php" class="btn">
                    + Buat Pengaduan
                </a>

                <a href="data_pengaduan.php" class="btn-outline" style="margin-left:10px;">
                    Lihat Riwayat Pengaduan
                </a>

            </div>


        </div>

    </div>

</body>

</html>