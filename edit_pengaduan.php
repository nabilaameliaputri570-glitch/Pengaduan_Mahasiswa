<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID pengaduan tidak ditemukan.");
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM pengaduan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

if (!$hasil) {
    die("Terjadi kesalahan saat mengambil data pengaduan.");
}

if (mysqli_num_rows($hasil) == 0) {
    die("Data pengaduan tidak ditemukan.");
}

$data = mysqli_fetch_assoc($hasil);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Pengaduan - Universitas Almarisah Madani</title>

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
            <span style="margin-right: 15px;">
                <?php
                echo htmlspecialchars(
                    $_SESSION['nama_admin'] ?? 'Admin',
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </span>

            <a href="logout_admin.php" style="color: white;">
                Logout
            </a>
        </div>

    </div>

    <div class="dashboard-layout">

        <div class="sidebar">

            <div class="sidebar-title">
                MENU ADMIN
            </div>

            <a href="dashboard_admin.php">
                🏠 Dashboard
            </a>

            <a href="dashboard_admin.php" class="active">
                📋 Data Pengaduan
            </a>

            <a href="logout_admin.php">
                🚪 Logout
            </a>

        </div>

        <div class="main-content">

            <div class="welcome-card">

                <h2>Kelola Pengaduan</h2>

                <p>
                    Periksa pengaduan mahasiswa dan berikan
                    status serta tanggapan.
                </p>

            </div>

            <div class="card">

                <h2 class="title">
                    Detail Pengaduan Mahasiswa
                </h2>

                <div style="
                    background: #f9edef;
                    padding: 20px;
                    border-radius: 10px;
                    margin-bottom: 25px;
                ">

                    <p>
                        <strong>NIM:</strong>
                        <?php
                        echo htmlspecialchars($data['nim'] ?? '', ENT_QUOTES, 'UTF-8');
                        ?>
                    </p>

                    <p style="margin-top: 10px;">
                        <strong>Nama:</strong>
                        <?php
                        echo htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8');
                        ?>
                    </p>

                    <p style="margin-top: 10px;">
                        <strong>Kategori:</strong>
                        <?php
                        echo htmlspecialchars($data['kategori'] ?? '', ENT_QUOTES, 'UTF-8');
                        ?>
                    </p>

                    <p style="margin-top: 10px;">
                        <strong>Tanggal:</strong>
                        <?php
                        echo htmlspecialchars($data['tanggal'] ?? '', ENT_QUOTES, 'UTF-8');
                        ?>
                    </p>

                </div>

                <div class="form-group">

                    <label>Isi Pengaduan</label>

                    <div style="
                        background: #f8f8f8;
                        border: 1px solid #ddd;
                        border-radius: 7px;
                        padding: 15px;
                        line-height: 1.6;
                        white-space: pre-wrap;
                    ">
                        <?php
                        echo htmlspecialchars(
                            $data['isi_pengaduan'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </div>

                </div>

                <form
                    action="proses_update_pengaduan.php"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo (int) $data['id']; ?>"
                    >

                    <div class="form-group">

                        <label>Status Pengaduan</label>

                        <select name="status" required>

                            <option
                                value="Menunggu"
                                <?php echo ($data['status'] == "Menunggu") ? "selected" : ""; ?>
                            >
                                Menunggu
                            </option>

                            <option
                                value="Diproses"
                                <?php echo ($data['status'] == "Diproses") ? "selected" : ""; ?>
                            >
                                Diproses
                            </option>

                            <option
                                value="Selesai"
                                <?php echo ($data['status'] == "Selesai") ? "selected" : ""; ?>
                            >
                                Selesai
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Tanggapan Admin</label>

                        <textarea
                            name="tanggapan"
                            placeholder="Tuliskan tanggapan untuk mahasiswa..."
                        ><?php
                        echo htmlspecialchars(
                            $data['tanggapan'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?></textarea>

                    </div>

                    <div class="form-group">

                        <label>Foto Bukti Tanggapan (Opsional)</label>

                        <input
                            type="file"
                            name="foto_tanggapan"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        >

                        <small>
                            Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                            Kosongkan jika tidak ingin mengganti foto.
                        </small>

                    </div>

                    <?php if (!empty($data['foto_tanggapan'])): ?>

                        <div class="form-group">

                            <label>Foto Bukti yang Tersimpan</label>

                            <div>
                                <img
                                    src="uploads_tanggapan/<?php
                                    echo rawurlencode(basename($data['foto_tanggapan']));
                                    ?>"
                                    alt="Foto bukti tanggapan admin"
                                    style="
                                        max-width: 300px;
                                        width: 100%;
                                        height: auto;
                                        border-radius: 8px;
                                        border: 1px solid #ddd;
                                    "
                                >
                            </div>

                        </div>

                    <?php endif; ?>

                    <button type="submit" class="btn">
                        Simpan Perubahan
                    </button>

                    <a
                        href="dashboard_admin.php"
                        class="btn-outline"
                        style="margin-left: 10px;"
                    >
                        ← Kembali
                    </a>

                </form>

            </div>

        </div>

    </div>

</body>
</html>
```