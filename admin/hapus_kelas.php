<?php
require_once '../koneksi.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus kelas berdasarkan ID
    $query = "DELETE FROM kelas WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        // Redirect ke halaman kelas setelah berhasil menghapus
        header('Location: kelas.php');
        exit;
    } else {
        // Jika gagal menghapus
        echo "Gagal menghapus kelas: " . mysqli_error($conn);
    }
} else {
    // Jika ID tidak ada
    echo "ID kelas tidak ditemukan.";
}
?>
