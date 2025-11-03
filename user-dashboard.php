<?php
session_start();
include('dbconnection.php');

//Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id_number, username, email, role, department FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    $qrDir = "qrcodes/";
    $qrFile = $qrDir . $user['id_number'] . ".png";

    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0777, true);
    }

    if (!file_exists($qrFile)) {
        include('phpqrcode/qrlib.php');

        $qrText = $user['id_number'] . "&" . $user['username'] . "&" . $user['department'];

        QRcode::png($qrText, $qrFile, QR_ECLEVEL_L, 5);
    }

} catch (PDOException $e) {
    die("Error fetching user info: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Student Parliament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Student Parliament</a>
            <div class="ms-auto">
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow text-center">
            <div class="card-body">
                <h3>Welcome, <?= htmlspecialchars($user['username']); ?>!</h3>
                <p class="text-muted mb-1"><?= htmlspecialchars($user['email']); ?></p>
                <p class="text-muted"><strong>Department:</strong> <?= htmlspecialchars($user['department']); ?></p>
                <span class="badge bg-info text-dark mb-3">Role: <?= htmlspecialchars($user['role']); ?></span>

                <hr>
                <h5 class="fw-bold">🎟 Your QR Code</h5>
                <p class="text-muted">Use this QR code to scan for attendance in every event.</p>

                <div class="d-flex flex-column align-items-center">
                    <img src="<?= htmlspecialchars($qrFile); ?>" alt="Your QR Code" 
                         class="border p-2 rounded bg-white shadow-sm" width="200">
                    <p class="mt-2"><strong>ID Number:</strong> <?= htmlspecialchars($user['id_number']); ?></p>

                    <!--Download Button -->
                    <a href="<?= htmlspecialchars($qrFile); ?>" download="<?= htmlspecialchars($user['id_number']); ?>_QR.png"
                       class="btn btn-outline-primary btn-sm mt-2">
                       ⬇️ Download QR Code
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h4 class="fw-bold">Your Dashboard</h4>
            <hr>

            <div class="row g-3">
                <!-- Events -->
                <div class="col-md-3">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body text-center">
                            <h5>📅 Institutional Events</h5>
                            <p>View upcoming events and activities.</p>
                            <a href="institutional_event_user.php" class="btn btn-primary btn-sm">View Events</a>
                        </div>
                    </div>
                </div>

                <!-- Attendance -->
                <div class="col-md-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-body text-center">
                            <h5>🕒 Attendance</h5>
                            <p>Check your attendance record.</p>
                            <a href="student_attendance.php" class="btn btn-success btn-sm">View Attendance</a>
                        </div>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="col-md-3">
                    <div class="card border-warning shadow-sm">
                        <div class="card-body text-center">
                            <h5>📋 Announcements</h5>
                            <p>Stay updated with latest news.</p>
                            <a href="user_announcement.php" class="btn btn-warning btn-sm">View</a>
                        </div>
                    </div>
                </div>

                <!-- Fines -->
                <div class="col-md-3">
                    <div class="card border-danger shadow-sm">
                        <div class="card-body text-center">
                            <h5>💰 Fines</h5>
                            <p>View and manage your unpaid fines.</p>
                            <a href="user_view_fines.php" class="btn btn-danger btn-sm">View Fines</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>