<?php
include('dbconnection.php');

try {
    $stmt = $pdo->query("
        SELECT 
            f.*, 
            u.username AS student_name,
            e.event_name
        FROM fines f
        INNER JOIN users u ON f.user_id = u.id
        INNER JOIN institutional_events e ON f.event_id = e.id
        ORDER BY f.date DESC
    ");
    $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database Error: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Fines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="text-center mb-4">💰 Student Fines Record</h3>

    <!--Search Bar -->
    <div class="d-flex justify-content-center mb-3">
        <input 
            type="text" 
            id="searchInput" 
            class="form-control w-50" 
            placeholder="🔍 Search for student, event, or date..."
        >
    </div>

    <!--Table -->
    <table class="table table-bordered table-striped" id="finesTable">
        <thead class="table-dark text-center">
            <tr>
                <th>Student Name</th>
                <th>Event</th>
                <th>Date</th>
                <th>Missing Scans</th>
                <th>Total Fine (₱)</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php if (empty($fines)): ?>
                <tr><td colspan="5">No fines recorded yet.</td></tr>
            <?php else: ?>
                <?php foreach ($fines as $fine): ?>
                    <tr>
                        <td><?= htmlspecialchars($fine['student_name']) ?></td>
                        <td><?= htmlspecialchars($fine['event_name']) ?></td>
                        <td><?= htmlspecialchars($fine['date']) ?></td>
                        <td><?= htmlspecialchars($fine['missing_scans']) ?></td>
                        <td><?= number_format($fine['total_fine'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!--Back to Dashboard -->
    <div class="text-center mt-4">
        <a href="user-dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
    </div>
</div>

<!-- External JS -->
<script src="search.js"></script>
</body>
</html>
