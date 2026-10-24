<?php
session_start();

if (!isset($_SESSION['nim'])) {
    header("Location: login.php");
    exit();
}

// Token unik untuk setiap formulir pengaduan.
$token_pengiriman = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buat Pengaduan - Universitas Almarisah Madani</title>

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
            <a href="logout.php" style="color:white;">Logout</a>
        </div>
    </div>

    <div class="dashboard-layout">

        <div class="sidebar">
            <div class="sidebar-title">MENU MAHASISWA</div>

            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="pengaduan.php" class="active">📝 Buat Pengaduan</a>
            <a href="data_pengaduan.php">📋 Riwayat Pengaduan</a>
            <a href="logout.php">🚪 Logout</a>
        </div>

        <div class="main-content">

            <div class="welcome-card">
                <h2>Buat Pengaduan</h2>

                <p>
                    Silakan sampaikan pengaduan atau keluhan Anda
                    melalui formulir di bawah ini.
                </p>
            </div>

            <div class="card">
                <h2 class="title">Form Pengaduan Mahasiswa</h2>

                <div style="background:#f9edef; padding:15px; border-radius:8px; margin-bottom:25px;">

                    <p>
                        <strong>Nama:</strong>
                        <?php
                        echo htmlspecialchars(
                            $_SESSION['nama'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

                    <p style="margin-top:7px;">
                        <strong>NIM:</strong>
                        <?php
                        echo htmlspecialchars(
                            $_SESSION['nim'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

                    <p style="margin-top:7px;">
                        <strong>Program Studi:</strong>
                        <?php
                        echo htmlspecialchars(
                            $_SESSION['prodi'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

                </div>

                <form
                    id="formPengaduan"
                    action="proses_pengaduan.php"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <!-- Token untuk mencegah pengiriman yang sama -->
                    <input
                        type="hidden"
                        name="token_pengiriman"
                        value="<?php
                        echo htmlspecialchars(
                            $token_pengiriman,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                    >

                    <div class="form-group">
                        <label>Kategori Pengaduan</label>

                        <select name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Fasilitas">Fasilitas</option>
                            <option value="Administrasi">Administrasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Isi Pengaduan</label>

                        <textarea
                            name="isi_pengaduan"
                            placeholder="Tuliskan pengaduan atau keluhan Anda..."
                            required
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label>Foto Bukti Pengaduan (Opsional)</label>

                        <input
                            type="file"
                            name="foto_bukti"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        >

                        <small>
                            Pilih foto berformat JPG, PNG, atau WEBP.
                            Ukuran maksimal 2 MB.
                        </small>
                    </div>

                    <button
                        type="submit"
                        id="tombolKirim"
                        class="btn"
                    >
                        Kirim Pengaduan
                    </button>

                    <a
                        href="dashboard.php"
                        class="btn-outline"
                        style="margin-left:10px;"
                    >
                        Batal
                    </a>

                </form>
            </div>

        </div>
    </div>

    <!-- Mencegah tombol diklik berulang kali -->
    <script>
    document.getElementById('formPengaduan').addEventListener('submit', function () {
        const tombol = document.getElementById('tombolKirim');

        tombol.disabled = true;
        tombol.textContent = 'Sedang Mengirim...';
    });
    </script>

</body>
</html>