<?php
session_start();
require_once('../config/koneksi.php'); // Include the database connection file

// Ensure the user is logged in and has the "admin" role
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

$username = $_SESSION['username']; // Retrieve the username from the session

// Fetch user details based on username
$sql_user = "SELECT role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $username);
$stmt->execute();
$result_user = $stmt->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $user_role = $user['role']; // Role (anggota or admin)
} else {
    echo "User not found!";
    exit();
}

// Check if the user is an admin
if ($user_role !== 'admin') {
    echo "You are not authorized to view this page.";
    exit();
}

// Fetch attendance data
$sql = "
    SELECT 
        a.username, 
        k.nama_kelas, 
        a.check_in_time 
    FROM 
        attendance a 
    JOIN 
        kelas k ON a.kelas_id = k.id
    ORDER BY 
        a.check_in_time DESC
";
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <!-- Include Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Laporan Kehadiran Kelas</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama Kelas</th>
                        <th>Waktu Check-in</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row['check_in_time'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">No attendance records found.</div>
        <?php endif; ?>

        <div class="mt-3">
            <!-- Button to go back to admin dashboard -->
            <a href="admin_dashboard.php" class="btn btn-secondary">
                Kembali ke Dashboard Admin
            </a>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
