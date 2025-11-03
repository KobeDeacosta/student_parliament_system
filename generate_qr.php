<?php
session_start();
require_once 'dbconnection.php';
require_once 'phpqrcode/qrlib.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id_number, username, department FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Student not found.");
}

$id_number = $user['id_number'];
$username  = $user['username'];
$department = $user['department'];

$qrDir = 'qrcodes/';
if (!file_exists($qrDir)) {
    mkdir($qrDir, 0777, true);
}

$qrData = $id_number . '&' . $username . '&' . $department;

$qrFileName = 'student_' . $user_id . '.png';
$qrFilePath = $qrDir . $qrFileName;

QRcode::png($qrData, $qrFilePath, QR_ECLEVEL_L, 6);

$update = $pdo->prepare("UPDATE users SET qr_code = ? WHERE id = ?");
$update->execute([$qrFilePath, $user_id]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container text-center py-5">
    <h2 class="mb-4">🎫 Your Attendance QR Code</h2>

    <div class="card mx-auto p-4" style="max-width: 400px;">
        <img src="<?= $qrFilePath ?>" alt="Your QR Code" class="img-fluid mb-3">
        <h5><?= htmlspecialchars($username) ?></h5>
        <p class="mb-1">ID Number: <strong><?= htmlspecialchars($id_number) ?></strong></p>
        <p>Department: <strong><?= htmlspecialchars($department) ?></strong></p>
        <a href="student-dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
