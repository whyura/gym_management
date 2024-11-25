<?php
session_start();
include '../config/koneksi.php';

// Cek role admin
if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    if ($action == 'add') {
        $nama_kelas = $_POST['nama_kelas'];
        $nama_instruktur = $_POST['nama_instruktur'];
        $jadwal = $_POST['jadwal'];
        $peserta = $_POST['peserta'];
        $biaya = $_POST['biaya'];
        $stmt = $conn->prepare("INSERT INTO kelas (nama_kelas, nama_instruktur, jadwal, peserta, biaya) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdi", $nama_kelas, $nama_instruktur, $jadwal, $peserta, $biaya);
        if ($stmt->execute()) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Kelas berhasil ditambahkan!';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Gagal menambahkan kelas!';
        }
    } elseif ($action == 'edit') {
        $id = $_POST['id'];
        $nama_kelas = $_POST['nama_kelas'];
        $nama_instruktur = $_POST['nama_instruktur'];
        $jadwal = $_POST['jadwal'];
        $peserta = $_POST['peserta'];
        $biaya = $_POST['biaya'];
        $stmt = $conn->prepare("UPDATE kelas SET nama_kelas=?, nama_instruktur=?, jadwal=?, peserta=?, biaya=? WHERE id=?");
        $stmt->bind_param("sssdis", $nama_kelas, $nama_instruktur, $jadwal, $peserta, $biaya, $id);
        if ($stmt->execute()) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Kelas berhasil diperbarui!';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Gagal memperbarui kelas!';
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM kelas WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Kelas berhasil dihapus!';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Gagal menghapus kelas!';
        }
    }

    // Redirect to avoid form resubmission
    header("Location: kelas.php");
    exit();
}

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$result = $conn->query("SELECT * FROM kelas WHERE nama_kelas LIKE '%$search%'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kelas (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Manajemen Kelas</h1>

        <!-- Tombol untuk ke Dashboard -->
        <a href="admin_dashboard.php" class="btn btn-primary mb-3">Dashboard</a>

        <form class="d-flex my-4" method="GET" action="kelas.php">
            <input class="form-control me-2" type="search" name="search" placeholder="Cari kelas..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>

        <!-- Tombol untuk membuka modal Tambah Kelas -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Kelas</button>

        <!-- Modal untuk Menambah Kelas -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="kelas.php">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tambah Kelas Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="add">

                            <div class="mb-3">
                                <label for="nama_kelas" class="form-label">Nama Kelas</label>
                                <input type="text" class="form-control" name="nama_kelas" required>
                            </div>

                            <div class="mb-3">
                                <label for="nama_instruktur" class="form-label">Nama Instruktur</label>
                                <input type="text" class="form-control" name="nama_instruktur" required>
                            </div>

                            <div class="mb-3">
                                <label for="jadwal" class="form-label">Jadwal</label>
                                <input type="datetime-local" class="form-control" name="jadwal" required>
                            </div>

                            <div class="mb-3">
                                <label for="peserta" class="form-label">Peserta</label>
                                <input type="number" class="form-control" name="peserta" required>
                            </div>

                            <div class="mb-3">
                                <label for="biaya" class="form-label">Biaya</label>
                                <input type="number" class="form-control" name="biaya" step="0.01" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kelas</th>
                    <th>Instruktur</th>
                    <th>Jadwal</th>
                    <th>Peserta</th>
                    <th>Biaya</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($row['nama_instruktur']) ?></td>
                        <td><?= $row['jadwal'] ?></td>
                        <td><?= $row['peserta'] ?></td>
                        <td><?= number_format($row['biaya'], 2) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                            <form method="POST" action="kelas.php" class="d-inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(event)">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    
                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="kelas.php">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Kelas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                                            <input type="text" class="form-control" name="nama_kelas" value="<?= htmlspecialchars($row['nama_kelas']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nama_instruktur" class="form-label">Nama Instruktur</label>
                                            <input type="text" class="form-control" name="nama_instruktur" value="<?= htmlspecialchars($row['nama_instruktur']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jadwal" class="form-label">Jadwal</label>
                                            <input type="datetime-local" class="form-control" name="jadwal" value="<?= date('Y-m-d\TH:i', strtotime($row['jadwal'])) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="peserta" class="form-label">Peserta</label>
                                            <input type="number" class="form-control" name="peserta" value="<?= $row['peserta'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="biaya" class="form-label">Biaya</label>
                                            <input type="number" class="form-control" name="biaya" step="0.01" value="<?= $row['biaya'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- SweetAlert for delete -->
    <script>
        function confirmDelete(event) {
            event.preventDefault(); // Prevent form submission
            Swal.fire({
                title: 'Yakin ingin menghapus kelas ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit(); // Submit the form if confirmed
                }
            });
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
