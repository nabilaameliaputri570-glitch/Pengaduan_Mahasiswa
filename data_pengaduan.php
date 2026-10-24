<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['nim'])) {
    header("Location: login.php");
    exit();
}

$nim = $_SESSION['nim'];

$sql = "SELECT * FROM pengaduan WHERE nim = ? ORDER BY id DESC";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $nim);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

if (!$hasil) {
    die("Terjadi kesalahan saat mengambil data pengaduan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengaduan - Universitas Almarisah Madani</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .foto-link {
            display: inline-block;
            background: #8b1028;
            color: white;
            padding: 7px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin: 4px 0;
        }

        .foto-link:hover {
            background: #650b1d;
        }

        .foto-preview {
            display: block;
            max-width: 150px;
            width: 100%;
            height: auto;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .foto-tidak-ada {
            color: #777;
            font-size: 13px;
        }
    </style>
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
        <a href="pengaduan.php">📝 Buat Pengaduan</a>
        <a href="data_pengaduan.php" class="active">📋 Riwayat Pengaduan</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main-content">

        <div class="welcome-card">
            <h2>Riwayat Pengaduan</h2>
            <p>Berikut adalah daftar pengaduan yang telah Anda kirimkan.</p>
        </div>

        <div class="card">
            <h2 class="title">Data Pengaduan Saya</h2>

            <div style="background:#f9edef; padding:15px; border-radius:8px; margin-bottom:25px;">
                <p>
                    <strong>Nama:</strong>
                    <?php echo htmlspecialchars($_SESSION['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>

                </p>

                <p style="margin-top:7px;">
                    <strong>NIM:</strong>
                    <?php echo htmlspecialchars($_SESSION['nim'], ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <p style="margin-top:7px;">
                    <strong>Program Studi:</strong>
                    <?php echo htmlspecialchars($_SESSION['prodi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>

            <?php if (mysqli_num_rows($hasil) > 0): ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Isi Pengaduan</th>
                            <th>Tanggal</th>
                            <th>Foto Mahasiswa</th>
                            <th>Status</th>
                            <th>Tanggapan Admin</th>
                            <th>Foto Bukti Admin</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($hasil)):
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>

                            <td>
                                <?php echo htmlspecialchars($data['kategori'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </td>

                            <td>
                                <?php echo nl2br(htmlspecialchars($data['isi_pengaduan'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
                            </td>

                            <td style="white-space:nowrap;">
                                <?php
                                echo !empty($data['tanggal'])
                                    ? date("d-m-Y", strtotime($data['tanggal']))
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                $namaFoto = basename($data['foto_bukti'] ?? '');
                                $pathFoto = __DIR__ . '/uploads/' . $namaFoto;
                                $urlFoto = 'uploads/' . rawurlencode($namaFoto);
                                ?>

                                <?php if ($namaFoto !== '' && is_file($pathFoto)): ?>
                                    <a
                                        class="foto-link"
                                        href="<?php echo htmlspecialchars($urlFoto, ENT_QUOTES, 'UTF-8'); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >Lihat Foto</a>
                                <?php else: ?>
                                    <span class="foto-tidak-ada">Tidak ada foto</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (($data['status'] ?? '') === 'Menunggu'): ?>
                                    <span class="status status-menunggu">Menunggu</span>
                                <?php elseif (($data['status'] ?? '') === 'Diproses'): ?>
                                    <span class="status status-diproses">Diproses</span>
                                <?php elseif (($data['status'] ?? '') === 'Selesai'): ?>
                                    <span class="status status-selesai">Selesai</span>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($data['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($data['tanggapan'])): ?>
                                    <?php echo nl2br(htmlspecialchars($data['tanggapan'], ENT_QUOTES, 'UTF-8')); ?>
                                <?php else: ?>
                                    <span class="foto-tidak-ada">Belum ada tanggapan</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                $namaFotoAdmin = basename($data['foto_tanggapan'] ?? '');
                                $pathFotoAdmin = __DIR__ . '/uploads_tanggapan/' . $namaFotoAdmin;
                                $urlFotoAdmin = 'uploads_tanggapan/' . rawurlencode($namaFotoAdmin);
                                ?>

                                <?php if ($namaFotoAdmin !== '' && is_file($pathFotoAdmin)): ?>
                                    <a
                                        class="foto-link"
                                        href="<?php echo htmlspecialchars($urlFotoAdmin, ENT_QUOTES, 'UTF-8'); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >Lihat Foto Admin</a>

                                    <a
                                        href="<?php echo htmlspecialchars($urlFotoAdmin, ENT_QUOTES, 'UTF-8'); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <img
                                            class="foto-preview"
                                            src="<?php echo htmlspecialchars($urlFotoAdmin, ENT_QUOTES, 'UTF-8'); ?>"
                                            alt="Foto bukti tanggapan admin"
                                        >
                                    </a>
                                <?php else: ?>
                                    <span class="foto-tidak-ada">Belum ada foto admin</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php else: ?>

            <div style="text-align:center; padding:40px;">
                <h3 style="color:#8b1028;">Belum Ada Pengaduan</h3>
                <p style="margin:15px 0;">Anda belum mengirimkan pengaduan.</p>
                <a href="pengaduan.php" class="btn">+ Buat Pengaduan</a>
            </div>

            <?php endif; ?>

            <br>
            <a href="dashboard.php" class="btn-outline">
                ← Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>

</body>
</html>
