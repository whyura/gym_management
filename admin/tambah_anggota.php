<?php
// Cek login session
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Koneksi ke database
require '../config/koneksi.php';

// Variabel untuk menampung pesan error atau sukses
$error = '';
$success = '';

// Proses tambah anggota
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $durasi = $_POST['durasi'];  // Pilihan durasi keanggotaan
    $status_keanggotaan = $_POST['status_keanggotaan'];

    // Validasi input
    if (empty($nama) || empty($nomor_telepon) || empty($durasi)) {
        $error = 'Semua field harus diisi.';
    } else {
        // Tentukan tanggal mulai (tanggal saat ini)
        $tanggal_mulai = date('Y-m-d');
        
        // Tentukan tanggal berakhir berdasarkan durasi
        switch ($durasi) {
            case '1hari':
                $tanggal_berakhir = date('Y-m-d', strtotime("+1 day"));
                break;
            case '7hari':
                $tanggal_berakhir = date('Y-m-d', strtotime("+7 days"));
                break;
            case '30hari':
                $tanggal_berakhir = date('Y-m-d', strtotime("+30 days"));
                break;
            case '1tahun':
                $tanggal_berakhir = date('Y-m-d', strtotime("+1 year"));
                break;
            default:
                $tanggal_berakhir = $tanggal_mulai;
        }

        // Query untuk menambahkan anggota
        $query = "INSERT INTO anggota (nama, nomor_telepon, tanggal_mulai, tanggal_berakhir, status_keanggotaan)
                  VALUES ('$nama', '$nomor_telepon', '$tanggal_mulai', '$tanggal_berakhir', '$status_keanggotaan')";

        if (mysqli_query($conn, $query)) {
            $success = 'Anggota berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan anggota: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
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
        form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input, .form-group select {
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .back-btn {
            display: inline-block;
            padding: 10px;
            margin-top: 1rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Anggota</h1>

        <!-- Menampilkan pesan error atau sukses -->
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <!-- Form untuk tambah anggota -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Anggota</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="nomor_telepon">Nomor Telepon</label>
                <input type="text" id="nomor_telepon" name="nomor_telepon" required>
            </div>
            <div class="form-group">
                <label for="durasi">Durasi Keanggotaan</label>
                <select id="durasi" name="durasi" required>
                    <option value="1hari">1 Hari</option>
                    <option value="7hari">7 Hari</option>
                    <option value="30hari">30 Hari</option>
                    <option value="1tahun">1 Tahun</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status_keanggotaan">Status Keanggotaan</label>
                <select id="status_keanggotaan" name="status_keanggotaan" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>
            <button type="submit">Tambah Anggota</button>
        </form>

        <!-- Link Kembali ke Data Anggota -->
        <a href="data_anggota.php" class="back-btn">Kembali ke Daftar Anggota</a>
    </div>

    <script>
        // SweetAlert untuk konfirmasi setelah menambahkan anggota
        <?php if ($success): ?>
            Swal.fire({
                title: 'Sukses!',
                text: 'Anggota berhasil ditambahkan!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'data_anggota.php';
            });
        <?php endif; ?>
    </script>
</body>
</html>
