<?php
// Cek login session
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Include koneksi
require_once '../config/koneksi.php';

// Ambil data anggota
$query = "SELECT * FROM anggota";
if (isset($_POST['search'])) {
    $search_term = $_POST['search_term'];
    $query = "SELECT * FROM anggota WHERE username LIKE '%$search_term%' OR no_telepon LIKE '%$search_term%'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Daftar Anggota</h2>

        <!-- Pencarian Anggota -->
        <form method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" name="search_term" class="form-control" placeholder="Cari anggota..." required>
                <div class="input-group-append">
                    <button type="submit" name="search" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>

        <!-- Tabel Anggota -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Username</th>
                        <th>No Telepon</th>
                        <th>Status Keanggotaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status = (strtotime($row['tanggal_berakhir']) > time()) ? 'Aktif' : 'Tidak Aktif';
                            echo "<tr>
                                    <td>" . $row['username'] . "</td>
                                    <td>" . $row['no_telepon'] . "</td>
                                    <td>" . $status . "</td>
                                    <td>
                                        <a href='edit_anggota.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='hapus_anggota.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Hapus</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>Tidak ada anggota terdaftar.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <a href="admin_dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
