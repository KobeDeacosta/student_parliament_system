<?php
session_start();
include('dbconnection.php');

// 🔒 Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 🗓 Fetch events (for all departments)
try {
    $today = date('Y-m-d');

    // Ongoing Events (event_date = today)
    $ongoingStmt = $pdo->prepare("SELECT * FROM institutional_events WHERE event_date = ? ORDER BY event_date ASC");
    $ongoingStmt->execute([$today]);
    $ongoingEvents = $ongoingStmt->fetchAll(PDO::FETCH_ASSOC);

    // Upcoming Events (event_date > today)
    $upcomingStmt = $pdo->prepare("SELECT * FROM institutional_events WHERE event_date > ? ORDER BY event_date ASC");
    $upcomingStmt->execute([$today]);
    $upcomingEvents = $upcomingStmt->fetchAll(PDO::FETCH_ASSOC);

    // Past Events (event_date < today)
    $pastStmt = $pdo->prepare("SELECT * FROM institutional_events WHERE event_date < ? ORDER BY event_date DESC");
    $pastStmt->execute([$today]);
    $pastEvents = $pastStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching events: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Institutional Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="user-dashboard.php">⬅ Back to Dashboard</a>
  </div>
</nav>

<div class="container mt-5">
  <h2 class="text-center mb-4">Institutional Events (All Departments)</h2>

  <!-- 🟡 Ongoing Events -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-warning text-dark">
      <h5 class="mb-0">🟡 Ongoing Events (<?= date('F d, Y'); ?>)</h5>
    </div>
    <div class="card-body">
      <?php if (count($ongoingEvents) > 0): ?>
        <table class="table table-striped text-center">
          <thead class="table-light">
            <tr>
              <th>Event Name</th>
              <th>Department</th>
              <th>Date</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ongoingEvents as $event): ?>
              <tr>
                <td><?= htmlspecialchars($event['event_name']); ?></td>
                <td><?= htmlspecialchars($event['department'] ?? 'All'); ?></td>
                <td><?= htmlspecialchars(date('F d, Y', strtotime($event['event_date']))); ?></td>
                <td><?= htmlspecialchars($event['description']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted text-center mb-0">No ongoing events today.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- 🔵 Upcoming Events -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">📅 Upcoming Events</h5>
    </div>
    <div class="card-body">
      <?php if (count($upcomingEvents) > 0): ?>
        <table class="table table-striped text-center">
          <thead class="table-light">
            <tr>
              <th>Event Name</th>
              <th>Department</th>
              <th>Date</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($upcomingEvents as $event): ?>
              <tr>
                <td><?= htmlspecialchars($event['event_name']); ?></td>
                <td><?= htmlspecialchars($event['department'] ?? 'All'); ?></td>
                <td><?= htmlspecialchars(date('F d, Y', strtotime($event['event_date']))); ?></td>
                <td><?= htmlspecialchars($event['description']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted text-center mb-0">No upcoming events.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- ⚫ Past Events -->
  <div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
      <h5 class="mb-0">⏳ Past Events</h5>
    </div>
    <div class="card-body">
      <?php if (count($pastEvents) > 0): ?>
        <table class="table table-striped text-center">
          <thead class="table-light">
            <tr>
              <th>Event Name</th>
              <th>Department</th>
              <th>Date</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pastEvents as $event): ?>
              <tr>
                <td><?= htmlspecialchars($event['event_name']); ?></td>
                <td><?= htmlspecialchars($event['department'] ?? 'All'); ?></td>
                <td><?= htmlspecialchars(date('F d, Y', strtotime($event['event_date']))); ?></td>
                <td><?= htmlspecialchars($event['description']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted text-center mb-0">No past events recorded.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
