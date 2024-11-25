<?php
require_once '../config/koneksi.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data kelas berdasarkan ID
    $query = "SELECT * FROM kelas WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "Kelas tidak ditemukan.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = $_POST['nama_kelas'];
    $instruktur = $_POST['instruktur'];
    $jadwal = $_POST['jadwal']; // Format: YYYY-MM-DD HH:MM
    $biaya = $_POST['biaya'];

    // Query untuk memperbarui data kelas
    $query = "UPDATE kelas SET nama_kelas = '$nama_kelas', instruktur = '$instruktur', jadwal = '$jadwal', biaya = '$biaya' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        header('Location: kelas.php');
        exit;
    } else {
        $error = "Gagal memperbarui kelas: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kelas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center">Edit Kelas</h2>
        <a href="kelas.php" class="btn btn-secondary">Kembali</a>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="nama_kelas" class="form-label">Nama Kelas</label>
            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" value="<?= $row['nama_kelas'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="instruktur" class="form-label">Nama Instruktur</label>
            <input type="text" class="form-control" id="instruktur" name="instruktur" value="<?= $row['instruktur'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="jadwal" class="form-label">Jadwal Kelas (Tanggal dan Waktu)</label>
            <input type="datetime-local" class="form-control" id="jadwal" name="jadwal" value="<?= date('Y-m-d\TH:i', strtotime($row['jadwal'])) ?>" required>
        </div>
        <div class="mb-3">
            <label for="biaya" class="form-label">Biaya Pendaftaran</label>
            <input type="number" class="form-control" id="biaya" name="biaya" value="<?= $row['biaya'] ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update Kelas</button>
    </form>
</div>

</body>
</html>
