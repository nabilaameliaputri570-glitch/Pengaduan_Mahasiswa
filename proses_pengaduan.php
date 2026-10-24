<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['nim'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pengaduan.php");
    exit();
}

$nim = $_SESSION['nim'];
$nama = $_SESSION['nama'] ?? '';
$kategori = trim($_POST['kategori'] ?? '');
$isi_pengaduan = trim($_POST['isi_pengaduan'] ?? '');
$token = $_POST['token_pengiriman'] ?? '';

$tanggal = date("Y-m-d");
$status = "Menunggu";
$tanggapan = "";
$foto_bukti = null;

/* Validasi token pengiriman */
if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    echo "<script>
        alert('Token tidak valid. Silakan buka formulir kembali.');
        window.location='pengaduan.php';
    </script>";
    exit();
}

/* Validasi kategori dan isi pengaduan */
if ($kategori === '' || $isi_pengaduan === '') {
    echo "<script>
        alert('Kategori dan isi pengaduan wajib diisi!');
        window.location='pengaduan.php';
    </script>";
    exit();
}

/* Periksa apakah pesan yang sama sudah pernah dikirim
   oleh mahasiswa dengan NIM yang sama */
$sql_cek_pesan = "SELECT id
                  FROM pengaduan
                  WHERE nim = ?
                  AND LOWER(TRIM(isi_pengaduan)) =
                      LOWER(TRIM(?))
                  LIMIT 1";

$stmt_cek_pesan = mysqli_prepare($koneksi, $sql_cek_pesan);

if (!$stmt_cek_pesan) {
    die("Gagal memeriksa pengaduan: " .
        htmlspecialchars(mysqli_error($koneksi)));
}

mysqli_stmt_bind_param(
    $stmt_cek_pesan,
    "ss",
    $nim,
    $isi_pengaduan
);

mysqli_stmt_execute($stmt_cek_pesan);
mysqli_stmt_store_result($stmt_cek_pesan);

if (mysqli_stmt_num_rows($stmt_cek_pesan) > 0) {
    mysqli_stmt_close($stmt_cek_pesan);

    echo "<script>
        alert('Pengaduan dengan pesan tersebut sudah pernah dikirim!');
        window.location='pengaduan.php';
    </script>";
    exit();
}

mysqli_stmt_close($stmt_cek_pesan);

/* Periksa apakah token sudah pernah digunakan */
$sql_cek_token = "SELECT id
                  FROM pengaduan
                  WHERE token_pengiriman = ?";

$stmt_cek_token = mysqli_prepare($koneksi, $sql_cek_token);

if (!$stmt_cek_token) {
    die("Gagal memeriksa token: " .
        htmlspecialchars(mysqli_error($koneksi)));
}

mysqli_stmt_bind_param($stmt_cek_token, "s", $token);
mysqli_stmt_execute($stmt_cek_token);
mysqli_stmt_store_result($stmt_cek_token);

if (mysqli_stmt_num_rows($stmt_cek_token) > 0) {
    mysqli_stmt_close($stmt_cek_token);

    echo "<script>
        alert('Pengaduan ini sudah berhasil dikirim sebelumnya.');
        window.location='dashboard.php';
    </script>";
    exit();
}

mysqli_stmt_close($stmt_cek_token);

/* Proses upload foto bukti jika ada */
if (
    isset($_FILES['foto_bukti']) &&
    $_FILES['foto_bukti']['error'] !== UPLOAD_ERR_NO_FILE
) {
    if ($_FILES['foto_bukti']['error'] !== UPLOAD_ERR_OK) {
        die("Upload foto gagal. Silakan coba kembali.");
    }

    $file = $_FILES['foto_bukti'];
    $maksimal = 2 * 1024 * 1024;

    if ($file['size'] > $maksimal) {
        die("Ukuran foto maksimal 2 MB.");
    }

    $info = getimagesize($file['tmp_name']);

    if ($info === false) {
        die("File yang dipilih bukan gambar yang valid.");
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $jenis_gambar = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($jenis_gambar[$mime])) {
        die("Format foto harus JPG, PNG, atau WEBP.");
    }

    $folder_upload = __DIR__ . "/uploads/";

    if (!is_dir($folder_upload)) {
        if (!mkdir($folder_upload, 0755, true)) {
            die("Folder uploads gagal dibuat.");
        }
    }

    $nama_file = bin2hex(random_bytes(16)) . "." .
                 $jenis_gambar[$mime];

    $tujuan = $folder_upload . $nama_file;

    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        die("Foto gagal disimpan.");
    }

    $foto_bukti = $nama_file;
}

/* Simpan pengaduan beserta token */
$sql = "INSERT INTO pengaduan
        (nim, nama, kategori, isi_pengaduan, tanggal,
         status, tanggapan, foto_bukti, token_pengiriman)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    if ($foto_bukti !== null) {
        @unlink(__DIR__ . "/uploads/" . $foto_bukti);
    }

    die("Gagal menyiapkan pengaduan: " .
        htmlspecialchars(mysqli_error($koneksi)));
}

mysqli_stmt_bind_param(
    $stmt,
    "sssssssss",
    $nim,
    $nama,
    $kategori,
    $isi_pengaduan,
    $tanggal,
    $status,
    $tanggapan,
    $foto_bukti,
    $token
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    echo "<script>
        alert('Pengaduan berhasil dikirim!');
        window.location='dashboard.php';
    </script>";
} else {
    $kode_error = mysqli_stmt_errno($stmt);
    $pesan_error = mysqli_stmt_error($stmt);

    if ($foto_bukti !== null) {
        @unlink(__DIR__ . "/uploads/" . $foto_bukti);
    }

    mysqli_stmt_close($stmt);

    if ($kode_error === 1062) {
        echo "<script>
            alert('Pengaduan ini sudah pernah dikirim!');
            window.location='pengaduan.php';
        </script>";
    } else {
        echo "Pengaduan gagal dikirim: " .
            htmlspecialchars($pesan_error);
    }
}
?>