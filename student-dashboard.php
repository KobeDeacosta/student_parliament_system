<?php
session_start();
include('dbconnection.php');

// icheck if nakalog in na ang student
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 🟢 Retrieve user info from DB
try {
    $stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
} catch (PDOException $e) {
    die("Error fetching user: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard | Student Parliament</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
   <div class="container-fluid p-0">
      <div class="row g-0">
         <!-- Sidebar -->
         <div class="col-md-3 bg-dark text-white p-4 d-flex flex-column justify-content-between" style="height:100vh;">
            <div>
                <h4 class="mb-4">Menu</h4>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="institutional_events.php" class="nav-link text-white">Institutional Events</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="admin_scan_attendance.php" class="nav-link text-white">Students Attendance</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="admin_student_attendance.php" class="nav-link text-white">Department Attendance</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="view_fines.php" class="nav-link text-white">Students Fines</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="admin_dashboard.php" class="nav-link text-white">Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="admin_announcement.php" class="nav-link text-white">Make an Announcement</a>
                    </li>
                </ul>
            </div>
            <a href="logout.php" class="btn btn-danger w-100 mt-3">Logout</a>
         </div>

         <!-- Main Content with background -->
         <div class="col-md-9 position-relative bg-cover d-flex justify-content-center align-items-end text-center text-white">
            <div class="overlay"></div>

            <div class="welcome-box mb-5">
                <h4>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h4>
                <p>You are logged in as <strong><?= htmlspecialchars($_SESSION['role']); ?></strong>.</p>
                <p><b>Good Day City Collegians.</b></p>
            </div>
         </div>
      </div>
   </div>
</body>
</html>