<?php
session_start();
include('dbconnection.php');

// 🔒 Require student login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

try {
    // 🔔 Fetch all active announcements, newest first
    $stmt = $pdo->query("SELECT * FROM announcements WHERE status = 'active' ORDER BY posted_at DESC");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching announcements: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Announcements | Student Parliament</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    
.announcement-card {
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    padding: 15px 20px;
    background-color: #fff;
    transition: transform 0.1s;
}
.announcement-card:hover {
    transform: translateY(-2px);
}
.announcement-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}
.announcement-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 10px;
}
.announcement-title {
    font-weight: 600;
    font-size: 1.1rem;
}
.announcement-time {
    font-size: 0.85rem;
    color: #6c757d;
}
</style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">📢 Announcements</a>
        <div class="ms-auto">
            <a href="user-dashboard.php" class="btn btn-light btn-sm">⬅ Dashboard</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h2 class="text-center mb-5 fw-bold">Latest Announcements</h2>

    <?php if (count($announcements) > 0): ?>
        <?php foreach ($announcements as $announce): ?>
            <div class="announcement-card">
                <div class="announcement-header">
                    <div class="announcement-avatar">
                        <?= strtoupper(substr($announce['posted_by'],0,1)) ?>
                    </div>
                    <div>
                        <div class="announcement-title"><?= htmlspecialchars($announce['title']) ?></div>
                        <div class="announcement-time"><?= date('F d, Y h:i A', strtotime($announce['posted_at'])) ?> by <?= htmlspecialchars($announce['posted_by']) ?></div>
                    </div>
                </div>
                <div class="announcement-content mt-2">
                    <?= nl2br(htmlspecialchars($announce['content'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center text-muted">No announcements at the moment.</div>
    <?php endif; ?>
</div>

</body>
</html>
