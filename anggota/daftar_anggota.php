<?php
session_start();
include '../config/koneksi.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil data anggota dari database
$query = "SELECT * FROM anggota ORDER BY id DESC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Nama Gym</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="anggota_dashboard.php">Dashboard Anggota</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="anggota_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="daftar_anggota.php">Daftar Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="kelas.php">Kelas</a></li>
                    <li class="nav-item"><a class="nav-link" href="check_in.php">Check-In</a></li>
                    <li class="nav-item"><a class="nav-link" href="pembayaran.php">Pembayaran</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4">
        <h2 class="text-center">Daftar Anggota Gym</h2>

        <!-- Tabel Daftar Anggota -->
        <div class="table-responsive mt-4">
            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Nomor Telepon</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Berakhir</th>
                        <th>Status Keanggotaan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo htmlspecialchars($row['nomor_telepon']); ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal_mulai']); ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal_berakhir']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['status_keanggotaan'] === 'Aktif') ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo htmlspecialchars($row['status_keanggotaan']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada anggota yang terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
