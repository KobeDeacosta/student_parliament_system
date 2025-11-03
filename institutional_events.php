<?php
session_start();
include('dbconnection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$events = [];
$editMode = false;
$editEvent = [];
$today = date('Y-m-d');

try {
    $pdo->query("UPDATE institutional_events SET status = 'inactive' WHERE event_date < '$today'");
} catch (PDOException $e) {
    die("Error auto-deactivating past events: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $event_name = trim($_POST['event_name']);
    $event_date = trim($_POST['event_date']);
    $description = trim($_POST['description']);

    if (!empty($event_name) && !empty($event_date)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO institutional_events (event_name, event_date, description, status) VALUES (?, ?, ?, 'inactive')");
            $stmt->execute([$event_name, $event_date, $description]);
            $_SESSION['msg_success'] = "✅ Event added successfully!";
            header("Location: institutional_events.php");
            exit();
        } catch (PDOException $e) {
            die("Error adding event: " . $e->getMessage());
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM institutional_events WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['msg_success'] = "🗑 Event deleted successfully.";
        header("Location: institutional_events.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting event: " . $e->getMessage());
    }
}

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = (int) $_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM institutional_events WHERE id = ?");
        $stmt->execute([$id]);
        $editEvent = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error loading event for edit: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
    $id = $_POST['event_id'];
    $event_name = trim($_POST['event_name']);
    $event_date = trim($_POST['event_date']);
    $description = trim($_POST['description']);

    if (!empty($event_name) && !empty($event_date)) {
        try {
            $stmt = $pdo->prepare("UPDATE institutional_events SET event_name = ?, event_date = ?, description = ? WHERE id = ?");
            $stmt->execute([$event_name, $event_date, $description, $id]);
            $_SESSION['msg_success'] = "✏️ Event updated successfully!";
            header("Location: institutional_events.php");
            exit();
        } catch (PDOException $e) {
            die("Error updating event: " . $e->getMessage());
        }
    }
}

if (isset($_GET['activate'])) {
    $eventId = intval($_GET['activate']);
    try {
        
        $pdo->query("UPDATE institutional_events SET status = 'inactive'");

        $stmt = $pdo->prepare("UPDATE institutional_events SET status = 'active' WHERE id = ? AND event_date >= ?");
        $stmt->execute([$eventId, $today]);

        $_SESSION['msg_success'] = "✅ Event ID $eventId is now ACTIVE.";
        header("Location: institutional_events.php");
        exit();
    } catch (PDOException $e) {
        die("Error activating event: " . $e->getMessage());
    }
}

if (isset($_GET['deactivate'])) {
    $eventId = intval($_GET['deactivate']);
    try {
        $stmt = $pdo->prepare("UPDATE institutional_events SET status = 'inactive' WHERE id = ?");
        $stmt->execute([$eventId]);

        $stmt = $pdo->prepare("
            UPDATE attendance 
            SET am_in = IFNULL(am_in,'MISS'), 
                am_out = IFNULL(am_out,'MISS'), 
                pm_in = IFNULL(pm_in,'MISS'), 
                pm_out = IFNULL(pm_out,'MISS')
            WHERE event_id = ? AND DATE(date) = CURDATE()
        ");
        $stmt->execute([$eventId]);

        $_SESSION['msg_success'] = "✅ Event ID $eventId has been DEACTIVATED. Missing scans flagged.";
        header("Location: institutional_events.php");
        exit();
    } catch (PDOException $e) {
        die("Error deactivating event: " . $e->getMessage());
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM institutional_events ORDER BY event_date DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching events: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Institutional Events | Student Parliament</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">📅 Institutional Events</h2>

    <?php if (!empty($_SESSION['msg_success'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['msg_success']; ?></div>
        <?php unset($_SESSION['msg_success']); ?>
    <?php endif; ?>

    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="event_id" value="<?= $editMode ? htmlspecialchars($editEvent['id']) : '' ?>">

        <div class="col-md-4">
            <input type="text" name="event_name" class="form-control"
                   placeholder="Enter event name" required
                   value="<?= $editMode ? htmlspecialchars($editEvent['event_name']) : '' ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="event_date" class="form-control" required
                   value="<?= $editMode ? htmlspecialchars($editEvent['event_date']) : '' ?>">
        </div>
        <div class="col-md-4">
            <input type="text" name="description" class="form-control"
                   placeholder="Optional"
                   value="<?= $editMode ? htmlspecialchars($editEvent['description']) : '' ?>">
        </div>
        <div class="col-md-1 d-grid">
            <?php if ($editMode): ?>
                <button type="submit" name="update_event" class="btn btn-warning">Update</button>
            <?php else: ?>
                <button type="submit" name="add_event" class="btn btn-primary">Add</button>
            <?php endif; ?>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($events) > 0): ?>
                    <?php foreach ($events as $event): ?>
                        <tr class="text-center">
                            <td><?= htmlspecialchars($event['id']); ?></td>
                            <td><?= htmlspecialchars($event['event_name']); ?></td>
                            <td><?= htmlspecialchars($event['event_date']); ?></td>
                            <td><?= htmlspecialchars($event['description']); ?></td>
                            <td>
                                <?php if ($event['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?edit=<?= $event['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?delete=<?= $event['id']; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                                <?php if ($event['status'] === 'active'): ?>
                                    <a href="?deactivate=<?= $event['id']; ?>" class="btn btn-outline-danger btn-sm">Deactivate</a>
                                <?php else: ?>
                                    <a href="?activate=<?= $event['id']; ?>" class="btn btn-success btn-sm">Activate</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">No events found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!--  Back to Dashboard -->
    <div class="text-center mt-4">
        <a href="student-dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
    </div>
</div>
</body>
</html>
