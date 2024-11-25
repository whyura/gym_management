<?php
// Cek login session
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Koneksi ke database
require '../config/koneksi.php';

// Ambil ID anggota yang akan diedit
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM anggota WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $anggota = $result->fetch_assoc();
} else {
    header('Location: data_anggota.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_berakhir = $_POST['tanggal_berakhir'];
    $status_keanggotaan = $_POST['status_keanggotaan'];

    $update_query = "UPDATE anggota SET nama = ?, nomor_telepon = ?, tanggal_mulai = ?, tanggal_berakhir = ?, status_keanggotaan = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssi", $nama, $nomor_telepon, $tanggal_mulai, $tanggal_berakhir, $status_keanggotaan, $id);
    
    if ($stmt->execute()) {
        header('Location: data_anggota.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Anggota</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?= $anggota['nama'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" value="<?= $anggota['nomor_telepon'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= $anggota['tanggal_mulai'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_berakhir" class="form-label">Tanggal Berakhir</label>
                <input type="date" class="form-control" id="tanggal_berakhir" name="tanggal_berakhir" value="<?= $anggota['tanggal_berakhir'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="status_keanggotaan" class="form-label">Status Keanggotaan</label>
                <select class="form-select" id="status_keanggotaan" name="status_keanggotaan" required>
                    <option value="Aktif" <?= $anggota['status_keanggotaan'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Tidak Aktif" <?= $anggota['status_keanggotaan'] == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</body>
</html>
