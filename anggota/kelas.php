<?php
session_start();
require_once('../config/koneksi.php'); // Include the database connection file

// Ensure the user is logged in
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

// Fetch available classes
$sql = "SELECT id, nama_kelas, nama_instruktur, jadwal, biaya FROM kelas";
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Function to format the date in a more readable way
function format_date($datetime) {
    return date('d-m-Y H:i', strtotime($datetime));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes</title>
    <!-- Include Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Kelas Yang Tersedia!</h2>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['nama_kelas']); ?></h5>
                            <p class="card-text">Instructor: <?php echo htmlspecialchars($row['nama_instruktur']); ?></p>
                            <p class="card-text">Schedule: <?php echo format_date($row['jadwal']); ?></p>
                            <p class="card-text">Cost: Rp <?php echo number_format($row['biaya'], 2, ',', '.'); ?></p>

                            <?php if ($user_role === 'anggota'): ?>
                                <!-- If the user is an 'anggota', show the 'Check-in' button -->
                                <a href="check_in.php?kelas_id=<?php echo $row['id']; ?>" class="btn btn-primary">Daftar Kelas</a>
                            <?php else: ?>
                                <!-- If the user is not 'anggota', show a message (Admin, for example) -->
                                <p class="text-muted">Only members can register for classes.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="row mt-4">
            <div class="col text-center">
                <!-- Button to go back to member dashboard -->
                <a href="anggota_dashboard.php" class="btn btn-info w-50">
                    Kembali ke Dashboard Anggota
                </a>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
