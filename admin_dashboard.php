<?php
session_start();
include('dbconnection.php');

// 🔒 Require admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {
    // 👥 Total registered students
    $students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();

    // 🎉 Students who attended at least one event OR flagged 'MISS'
    $attended = $pdo->query("
        SELECT COUNT(DISTINCT user_id)
        FROM attendance
        WHERE am_in IS NOT NULL
           OR am_out IS NOT NULL
           OR pm_in IS NOT NULL
           OR pm_out IS NOT NULL
    ")->fetchColumn();

    $notAttended = $students - $attended;

    // 💰 Fines summary
    $finesTotal = $pdo->query("SELECT SUM(total_fine) FROM fines")->fetchColumn() ?? 0;
    $finedStudents = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM fines")->fetchColumn();

    // 📊 Fines per Event (all events)
    $fineDataStmt = $pdo->query("
        SELECT e.event_name, SUM(f.total_fine) AS total
        FROM institutional_events e
        LEFT JOIN fines f ON f.event_id = e.id
        GROUP BY e.id
        ORDER BY e.event_date DESC
    ");
    $fineData = $fineDataStmt->fetchAll(PDO::FETCH_ASSOC);

    // 🎓 Attendance per Event (count all attendance including 'MISS')
    $attendDataStmt = $pdo->query("
        SELECT e.event_name, COUNT(DISTINCT a.user_id) AS attendees
        FROM institutional_events e
        LEFT JOIN attendance a ON a.event_id = e.id
        GROUP BY e.id
        ORDER BY e.event_date DESC
    ");
    $attendData = $attendDataStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📊 Admin Dashboard | Student Parliament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">📊 Admin Dashboard</a>
        <div class="ms-auto">
            <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h2 class="text-center mb-5 fw-bold">📈 System Overview</h2>

    <!-- Summary Cards -->
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold text-primary"><?= $students; ?></h3>
                    <p class="text-muted">Registered Students</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold text-success"><?= $attended; ?></h3>
                    <p class="text-muted">Students Who Attended / FLAGGED</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold text-danger">₱<?= number_format($finesTotal, 2); ?></h3>
                    <p class="text-muted"><?= $finedStudents; ?> Students with Fines</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graph Section -->
    <div class="row">
        <!-- Attendance per Event -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">Attendance per Event</div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Fines per Event -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white fw-bold">Fines per Event</div>
                <div class="card-body">
                    <canvas id="finesChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Attendance Overview (Pie Chart) -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">Attendance Overview</div>
                <div class="card-body">
                    <canvas id="attendancePieChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 🎉 Attendance per Event
const attendData = {
    labels: <?= json_encode(array_column($attendData, 'event_name')); ?>,
    datasets: [{
        label: 'Number of Attendees (Including FLAGGED)',
        data: <?= json_encode(array_column($attendData, 'attendees')); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
    }]
};

new Chart(document.getElementById('attendanceChart'), {
    type: 'bar',
    data: attendData,
    options: { scales: { y: { beginAtZero: true } } }
});

// 💰 Fines per Event
const fineData = {
    labels: <?= json_encode(array_column($fineData, 'event_name')); ?>,
    datasets: [{
        label: 'Total Fines (₱)',
        data: <?= json_encode(array_column($fineData, 'total')); ?>,
        backgroundColor: 'rgba(255, 99, 132, 0.6)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1
    }]
};

new Chart(document.getElementById('finesChart'), {
    type: 'bar',
    data: fineData,
    options: { scales: { y: { beginAtZero: true } } }
});

// 🥧 Attendance Pie Chart
const attendancePie = {
    labels: ['Attended', 'Not Attended'],
    datasets: [{
        data: [<?= $attended; ?>, <?= $notAttended; ?>],
        backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(201, 203, 207, 0.6)'],
        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(201, 203, 207, 1)'],
        borderWidth: 1
    }]
};

new Chart(document.getElementById('attendancePieChart'), {
    type: 'pie',
    data: attendancePie
});
</script>

<!-- 🟢 Back to Dashboard -->
<div class="text-center mt-4">
    <a href="student-dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
</div>

</body>
</html>
