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

// Fetch class details
if (isset($_GET['kelas_id'])) {
    $kelas_id = $_GET['kelas_id'];

    // Fetch class details
    $sql = "SELECT * FROM kelas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kelas_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $kelas = $result->fetch_assoc();
    } else {
        echo "Class not found!";
        exit();
    }

    // Check if the member has already checked in
    $sql_checkin = "SELECT * FROM attendance WHERE kelas_id = ? AND username = ?";
    $stmt_checkin = $conn->prepare($sql_checkin);
    $stmt_checkin->bind_param("is", $kelas_id, $username);
    $stmt_checkin->execute();
    $result_checkin = $stmt_checkin->get_result();

    if ($result_checkin->num_rows > 0) {
        $check_in_message = "You have already checked in for this class.";
    } else {
        // Insert the check-in record
        $sql_insert = "INSERT INTO attendance (kelas_id, username, check_in_time) VALUES (?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("is", $kelas_id, $username);
        if ($stmt_insert->execute()) {
            $check_in_message = "Successfully checked into the class: " . htmlspecialchars($kelas['nama_kelas']);
        } else {
            $check_in_message = "Error during check-in. Please try again.";
        }
    }
} else {
    echo "No class selected.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in to Class</title>
    <!-- Include Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include SweetAlert2 for popup alerts -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.15/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Class: <?php echo htmlspecialchars($kelas['nama_kelas']); ?></h4>
                        <p class="card-text"><strong>Instructor:</strong> <?php echo htmlspecialchars($kelas['nama_instruktur']); ?></p>
                        <p class="card-text"><strong>Schedule:</strong> <?php echo date('d-m-Y H:i', strtotime($kelas['jadwal'])); ?></p>
                        <p class="card-text"><strong>Cost:</strong> Rp <?php echo number_format($kelas['biaya'], 2, ',', '.'); ?></p>

                        <!-- Check-in message -->
                        <div class="alert alert-info mt-3">
                            <p><?php echo isset($check_in_message) ? $check_in_message : ''; ?></p>
                        </div>

                        <?php if ($user_role === 'anggota' && !isset($check_in_message)): ?>
                            <!-- If the user is an 'anggota' and hasn't checked in yet, show the 'Check-in' button -->
                            <a href="check_in.php?kelas_id=<?php echo $kelas['id']; ?>" class="btn btn-success w-100" id="checkin-button">
                                Daftar untuk Kelas Ini
                            </a>
                        <?php elseif ($user_role !== 'anggota'): ?>
                            <!-- If the user is not 'anggota', show a message -->
                            <div class="alert alert-warning">
                                Only members can register for this class.
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <!-- Button to go back to member dashboard -->
                            <a href="anggota_dashboard.php" class="btn btn-secondary w-100">
                                Kembali ke Dashboard Anggota
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert2 for popup alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.15/dist/sweetalert2.all.min.js"></script>
    <script>
        // SweetAlert for check-in success
        <?php if (isset($check_in_message) && strpos($check_in_message, 'Successfully') !== false): ?>
            Swal.fire({
                title: 'Check-in Successful!',
                text: '<?php echo $check_in_message; ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // SweetAlert for check-in already performed
        <?php if (isset($check_in_message) && strpos($check_in_message, 'already checked in') !== false): ?>
            Swal.fire({
                title: 'Already Checked-in',
                text: '<?php echo $check_in_message; ?>',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
