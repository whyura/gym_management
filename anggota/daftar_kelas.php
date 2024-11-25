<?php
require_once '../config/koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'anggota') {
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelas_id = $_POST['kelas_id'];
    $anggota_id = $_SESSION['user_id'];

    // Cek apakah sudah terdaftar
    $cekQuery = $conn->prepare("SELECT * FROM pendaftaran WHERE kelas_id = ? AND anggota_id = ?");
    $cekQuery->bind_param('ii', $kelas_id, $anggota_id);
    $cekQuery->execute();
    $cekResult = $cekQuery->get_result();

    if ($cekResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Anda sudah terdaftar di kelas ini.']);
        exit;
    }

    // Tambahkan ke pendaftaran
    $insertQuery = $conn->prepare("INSERT INTO pendaftaran (kelas_id, anggota_id) VALUES (?, ?)");
    $insertQuery->bind_param('ii', $kelas_id, $anggota_id);

    if ($insertQuery->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pendaftaran berhasil!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.']);
    }
}
?>
