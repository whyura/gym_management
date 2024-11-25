<?php
session_start();
include '../config/koneksi.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil informasi user dari sesi
$username = $_SESSION['username'];

// Query untuk mendapatkan data anggota berdasarkan username
$query = "SELECT a.*, u.username FROM anggota a 
          JOIN users u ON a.id = u.id 
          WHERE u.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$anggota = $result->fetch_assoc();

// Cek apakah user sudah menjadi anggota
$is_anggota = $anggota ? true : false;

// Proses pendaftaran anggota
$success_message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_anggota) {
    $nama = $_POST['nama'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $durasi_keanggotaan = $_POST['durasi_keanggotaan'];

    // Hitung tanggal mulai dan tanggal berakhir
    $tanggal_mulai = date('Y-m-d');
    switch ($durasi_keanggotaan) {
        case '1hari':
            $tanggal_berakhir = date('Y-m-d', strtotime('+1 day'));
            break;
        case '7hari':
            $tanggal_berakhir = date('Y-m-d', strtotime('+7 days'));
            break;
        case '30hari':
            $tanggal_berakhir = date('Y-m-d', strtotime('+30 days'));
            break;
        case '1tahun':
            $tanggal_berakhir = date('Y-m-d', strtotime('+1 year'));
            break;
        default:
            $tanggal_berakhir = $tanggal_mulai;
    }

    // Simpan data ke tabel anggota
    $query = "INSERT INTO anggota (nama, nomor_telepon, tanggal_mulai, tanggal_berakhir, status_keanggotaan, created_at) 
              VALUES (?, ?, ?, ?, 'Aktif', NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $nama, $nomor_telepon, $tanggal_mulai, $tanggal_berakhir);

    if ($stmt->execute()) {
        $success_message = "Pendaftaran berhasil! Selamat, Anda sekarang anggota aktif.";
        // Reload data anggota
        $query = "SELECT a.*, u.username FROM anggota a 
                  JOIN users u ON a.id = u.id 
                  WHERE u.username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $anggota = $result->fetch_assoc();
        $is_anggota = true;
    } else {
        $error_message = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota - Nama Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Dashboard Anggota</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="anggota_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="daftar_anggota.php">Daftar Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="kelas.php">Kelas</a></li>
                    <li class="nav-item"><a class="nav-link" href="check_in.php">Check-In</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <h2 class="text-center">Selamat Datang, satrya!</h2>

        <!-- Pesan sukses setelah pendaftaran -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success mt-3" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Form atau data anggota -->
        <?php if (!$is_anggota): ?>
            <form action="" method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" id="nama" required>
                </div>
                <div class="mb-3">
                    <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" name="nomor_telepon" id="nomor_telepon" required>
                </div>
                <div class="mb-3">
                    <label for="durasi_keanggotaan" class="form-label">Durasi Keanggotaan</label>
                    <select name="durasi_keanggotaan" id="durasi_keanggotaan" class="form-select" required>
                        <option value="1hari">1 Hari</option>
                        <option value="7hari">7 Hari</option>
                        <option value="30hari">30 Hari</option>
                        <option value="1tahun">1 Tahun</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Daftar</button>
            </form>
        <?php else: ?>
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    Informasi Keanggotaan Anda
                </div>
                <div class="card-body">
                    <?php if ($anggota): ?>
                        <p><strong>Nama:</strong> <?php echo htmlspecialchars($anggota['nama'] ?? 'Tidak diketahui'); ?></p>
                        <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($anggota['nomor_telepon'] ?? 'Tidak diketahui'); ?></p>
                        <p><strong>Tanggal Mulai:</strong> <?php echo date('d-m-Y', strtotime($anggota['tanggal_mulai'])) ?? 'Tidak diketahui'; ?></p>
                        <p><strong>Tanggal Berakhir:</strong> <?php echo date('d-m-Y', strtotime($anggota['tanggal_berakhir'])) ?? 'Tidak diketahui'; ?></p>
                        <p><strong>Status Keanggotaan:</strong> <?php echo htmlspecialchars($anggota['status_keanggotaan'] ?? 'Tidak diketahui'); ?></p>
                    <?php else: ?>
                        <p>Data keanggotaan tidak ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
