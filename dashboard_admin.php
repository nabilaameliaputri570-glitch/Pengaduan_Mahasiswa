<?php
session_start();

include "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

$sql = "SELECT * FROM pengaduan ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Terjadi kesalahan: " . mysqli_error($koneksi));
}

$total = mysqli_num_rows($hasil);
$menunggu = 0;
$diproses = 0;
$selesai = 0;
$data_pengaduan = [];

while ($data = mysqli_fetch_assoc($hasil)) {
    $data_pengaduan[] = $data;

    if ($data['status'] == "Menunggu") {
        $menunggu++;
    }

    if ($data['status'] == "Diproses") {
        $diproses++;
    }

    if ($data['status'] == "Selesai") {
        $selesai++;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .table-container {
            overflow-x: auto;
        }

        table th,
        table td {
            vertical-align: middle;
        }

        .foto-link {
            display: inline-block;
            padding: 7px 12px;
            background: #8b1028;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            white-space: nowrap;
        }

        .foto-link:hover {
            background: #650b1c;
        }

        .foto-tidak-ada {
            color: #999;
            font-size: 13px;
        }

        table th:nth-child(6),
        table td:nth-child(6) {
            min-width: 120px;
            white-space: nowrap;
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
            <span style="margin-right: 15px;">
                <?php echo htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin'); ?>
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

            <a href="dashboard_admin.php" class="active">
                🏠 Dashboard
            </a>

            <a href="dashboard_admin.php">
                📋 Data Pengaduan
            </a>

            <a href="logout_admin.php">
                🚪 Logout
            </a>
        </div>

        <div class="main-content">

            <div class="welcome-card">
                <h2>
                    Selamat Datang,
                    <?php echo htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin'); ?>!
                </h2>

                <p>
                    Kelola dan pantau pengaduan mahasiswa
                    Universitas Almarisah Madani.
                </p>
            </div>

            <div class="info-cards">

                <div class="info-card">
                    <h4>Total Pengaduan</h4>
                    <div class="number">
                        <?php echo $total; ?>
                    </div>
                </div>

                <div class="info-card">
                    <h4>Menunggu</h4>
                    <div class="number">
                        <?php echo $menunggu; ?>
                    </div>
                </div>

                <div class="info-card">
                    <h4>Diproses</h4>
                    <div class="number">
                        <?php echo $diproses; ?>
                    </div>
                </div>

                <div class="info-card">
                    <h4>Selesai</h4>
                    <div class="number">
                        <?php echo $selesai; ?>
                    </div>
                </div>

            </div>

            <div class="card">

                <h2 class="title">
                    Data Pengaduan Mahasiswa
                </h2>

                <p style="margin-bottom: 20px;">
                    Berikut adalah seluruh pengaduan yang telah
                    dikirimkan oleh mahasiswa.
                </p>

                <?php if ($total > 0) { ?>

                    <div class="table-container">

                        <table>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Isi Pengaduan</th>
                                <th>Tanggal</th>
                                <th>Foto Bukti</th>
                                <th>Status</th>
                                <th>Tanggapan</th>
                                <th>Aksi</th>
                            </tr>

                            <?php
                            $no = 1;

                            foreach ($data_pengaduan as $data) {
                                $namaFoto = basename($data['foto_bukti'] ?? '');
                            ?>

                                <tr>
                                    <td>
                                        <?php echo $no++; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($data['nim']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($data['nama']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($data['kategori']); ?>
                                    </td>

                                    <td>
                                        <?php echo nl2br(htmlspecialchars($data['isi_pengaduan'])); ?>
                                    </td>

                                    <td style="white-space: nowrap;">
                                        <?php
                                        echo date(
                                            "d-m-Y",
                                            strtotime($data['tanggal'])
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        $lokasiFoto = __DIR__ . "/uploads/" . $namaFoto;

                                        if (
                                            $namaFoto !== '' &&
                                            is_file($lokasiFoto)
                                        ) {
                                        ?>
                                            <a
                                                href="<?php echo 'uploads/' . rawurlencode($namaFoto); ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="foto-link"
                                            >
                                                Lihat Foto
                                            </a>
                                        <?php
                                        } elseif ($namaFoto !== '') {
                                        ?>
                                            <span class="foto-tidak-ada">
                                                File foto tidak ditemukan
                                            </span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="foto-tidak-ada">
                                                Tidak ada foto
                                            </span>
                                        <?php
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        if ($data['status'] == "Menunggu") {
                                            echo '<span class="status status-menunggu">Menunggu</span>';
                                        } elseif ($data['status'] == "Diproses") {
                                            echo '<span class="status status-diproses">Diproses</span>';
                                        } elseif ($data['status'] == "Selesai") {
                                            echo '<span class="status status-selesai">Selesai</span>';
                                        } else {
                                            echo htmlspecialchars($data['status']);
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        if ($data['tanggapan'] != "") {
                                            echo nl2br(htmlspecialchars($data['tanggapan']));
                                        } else {
                                            echo '<span style="color: #999;">Belum ada tanggapan</span>';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <a
                                            href="edit_pengaduan.php?id=<?php echo (int)$data['id']; ?>"
                                            class="action-link"
                                        >
                                            Kelola
                                        </a>
                                    </td>
                                </tr>

                            <?php } ?>

                        </table>

                    </div>

                <?php } else { ?>

                    <div style="text-align: center; padding: 50px;">
                        <h3 style="color: #8b1028;">
                            Belum Ada Pengaduan
                        </h3>

                        <p style="margin-top: 10px;">
                            Belum ada mahasiswa yang mengirimkan pengaduan.
                        </p>
                    </div>

                <?php } ?>

            </div>

        </div>
    </div>

</body>
</html>
```
