<?php
session_start();
include('dbconnection.php');

// 🔒 Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 🧾 Fetch fines for this student
try {
    $stmt = $pdo->prepare("SELECT * FROM students_fines WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching fines: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Fines | Student Parliament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="user-dashboard.php">⬅ Back to Dashboard</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">💰 Your Fines</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (count($fines) > 0): ?>
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-danger">
                        <tr>
                            <th>Fine Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fines as $fine): ?>
                            <tr>
                                <td><?= htmlspecialchars($fine['description']); ?></td>
                                <td>₱<?= htmlspecialchars(number_format($fine['amount'], 2)); ?></td>
                                <td>
                                    <?php if ($fine['status'] == 'Paid'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(date('F d, Y', strtotime($fine['date_issued']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted text-center mb-0">You have no fines recorded.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
