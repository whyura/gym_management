<?php
// Cek login session
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Koneksi ke database
require '../config/koneksi.php';

// Hapus anggota berdasarkan ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM anggota WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header('Location: data_anggota.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    header('Location: data_anggota.php');
    exit;
}
