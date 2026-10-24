<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard_admin.php");
    exit();
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';
$tanggapan = trim($_POST['tanggapan'] ?? '');

if (!$id || !in_array($status, ['Menunggu', 'Diproses', 'Selesai'], true)) {
    die("ID atau status pengaduan tidak valid.");
}

// Periksa apakah pengaduan tersedia dan ambil foto sebelumnya.
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT foto_tanggapan FROM pengaduan WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($hasil) === 0) {
    die("Data pengaduan tidak ditemukan.");
}

$data_lama = mysqli_fetch_assoc($hasil);
$foto_tanggapan = $data_lama['foto_tanggapan'] ?? null;

// Proses upload foto jika admin memilih file.
if (isset($_FILES['foto_tanggapan']) &&
    $_FILES['foto_tanggapan']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['foto_tanggapan']['error'] !== UPLOAD_ERR_OK) {
        die("Upload foto gagal. Silakan coba kembali.");
    }

    $file = $_FILES['foto_tanggapan'];

    // Maksimal ukuran file 2 MB.
    if ($file['size'] > 2 * 1024 * 1024) {
        die("Ukuran foto maksimal 2 MB.");
    }

    // Periksa tipe file berdasarkan isi file, bukan hanya nama.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $format_diizinkan = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($format_diizinkan[$mime])) {
        die("Format foto harus JPG, PNG, atau WEBP.");
    }

    // Pastikan folder upload tersedia.
    $folder = __DIR__ . '/uploads_tanggapan/';

    if (!is_dir($folder)) {
        if (!mkdir($folder, 0755, true)) {
            die("Folder upload foto gagal dibuat.");
        }
    }

    if (!is_writable($folder)) {
        die("Folder uploads_tanggapan tidak dapat ditulisi.");
    }

    // Buat nama file unik agar tidak menimpa foto lain.
    $nama_file = bin2hex(random_bytes(16))
        . '.'
        . $format_diizinkan[$mime];

    $tujuan = $folder . $nama_file;

    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        die("Foto gagal disimpan. Silakan coba kembali.");
    }

    $foto_tanggapan = $nama_file;
}

// Simpan status, tanggapan, dan nama foto ke database.
$stmt_update = mysqli_prepare(
    $koneksi,
    "UPDATE pengaduan
     SET status = ?, tanggapan = ?, foto_tanggapan = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt_update,
    "sssi",
    $status,
    $tanggapan,
    $foto_tanggapan,
    $id
);

if (mysqli_stmt_execute($stmt_update)) {
    echo "<script>
        alert('Pengaduan berhasil diperbarui!');
        window.location = 'dashboard_admin.php';
    </script>";
    exit();
} else {
    die("Gagal memperbarui pengaduan: "
        . htmlspecialchars(mysqli_error($koneksi), ENT_QUOTES, 'UTF-8'));
}
?>
```