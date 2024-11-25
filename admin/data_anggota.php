<?php
// Cek login session
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Koneksi ke database
require '../config/koneksi.php';

// Ambil data anggota
$query = "SELECT * FROM anggota";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Desain modern */
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #34495e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        table, th, td {
            border: 1px solid #ecf0f1;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        button {
            padding: 5px 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #c0392b;
        }

        a {
            display: inline-block;
            padding: 10px;
            margin-top: 1rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
        }

        a:hover {
            background-color: #2980b9;
        }

        .add-btn {
            display: inline-block;
            margin-bottom: 1rem;
            background-color: #2ecc71;
        }

        .add-btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Daftar Anggota</h1>

        <!-- Tombol Tambah Anggota -->
        <a href="tambah_anggota.php" class="add-btn">Tambah Anggota</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Nomor Telepon</th>
                    <th>Durasi Keanggotaan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['nomor_telepon'] ?></td>
                    <td>
                        <?php
                            // Mengubah format tanggal menjadi d-m-Y
                            $tanggal_mulai = new DateTime($row['tanggal_mulai']);
                            $tanggal_berakhir = new DateTime($row['tanggal_berakhir']);
                            $formatted_tanggal_mulai = $tanggal_mulai->format('d-m-Y');
                            $formatted_tanggal_berakhir = $tanggal_berakhir->format('d-m-Y');
                            
                            // Menghitung durasi keanggotaan
                            $interval = $tanggal_mulai->diff($tanggal_berakhir);
                            echo $interval->y . " Tahun " . $interval->m . " Bulan " . $interval->d . " Hari";
                        ?>
                    </td>
                    <td><?= $row['status_keanggotaan'] ?></td>
                    <td>
                        <a href="edit_anggota.php?id=<?= $row['id'] ?>">Edit</a>
                        <button class="btn-delete" onclick="confirmDelete(<?= $row['id'] ?>)">Hapus</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="back-btn">Kembali ke Dashboard</a>
    </div>

    <script>
        // SweetAlert untuk konfirmasi hapus anggota
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anggota ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_anggota.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>
