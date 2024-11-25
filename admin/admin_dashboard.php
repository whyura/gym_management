<?php
// Cek login session
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navigation */
        .navbar {
            background-color: #34495e;
            color: white;
            padding: 0.8rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .navbar a {
            text-decoration: none;
            color: white;
            margin: 0 1rem;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #f1c40f;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        /* Main Content */
        .main {
            flex: 1;
            padding: 2rem;
        }

        .main h2 {
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #2c3e50;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .feature {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .feature h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .feature p {
            color: #555;
            font-size: 1rem;
        }

        .feature a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <button class="logout-btn" onclick="window.location.href='../logout.php'">Logout</button>
    </div>

    <!-- Main Content -->
    <div class="main">
        <h2>Selamat Datang, Admin!</h2>
        <div class="features">
            <div class="feature">
                <h3>Pengelolaan Data Anggota</h3>
                <p>Kelola data anggota gym dengan mudah.</p>
                <a href="data_anggota.php">Lihat Data Anggota</a>
            </div>
            <div class="feature">
                <h3>Jadwal Kelas Gym</h3>
                <p>Kelola jadwal kelas gym untuk peserta.</p>
                <a href="kelas.php">Atur Jadwal Kelas</a>
            </div>
            <div class="feature">
                <h3>Laporan Kehadiran</h3>
                <p>Lihat laporan statistik kehadiran anggota.</p>
                <a href="laporan_kehadiran.php">Lihat Laporan</a>
            </div>
        </div>
    </div>
</body>
</html>
