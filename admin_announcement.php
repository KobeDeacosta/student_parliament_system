<?php
session_start();
include('dbconnection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$announcements = [];
$editMode = false;
$editAnnounce = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $posted_by = $_SESSION['username'];

    if (!empty($title) && !empty($content)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, content, posted_by, status, posted_at) VALUES (?, ?, ?, 'active', NOW())");
            $stmt->execute([$title, $content, $posted_by]);
            $_SESSION['msg_success'] = "✅ Announcement added successfully!";
            header("Location: admin_announcement.php");
            exit();
        } catch (PDOException $e) {
            die("Error adding announcement: " . $e->getMessage());
        }
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['msg_success'] = "🗑 Announcement deleted successfully.";
        header("Location: admin_announcement.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting announcement: " . $e->getMessage());
    }
}

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = intval($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        $editAnnounce = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error loading announcement: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_announcement'])) {
    $id = $_POST['announcement_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        try {
            $stmt = $pdo->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
            $stmt->execute([$title, $content, $id]);
            $_SESSION['msg_success'] = "✏️ Announcement updated successfully!";
            header("Location: admin_announcement.php");
            exit();
        } catch (PDOException $e) {
            die("Error updating announcement: " . $e->getMessage());
        }
    }
}

if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    try {
        $stmt = $pdo->prepare("SELECT status FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        $status = $stmt->fetchColumn();
        $newStatus = ($status === 'active') ? 'inactive' : 'active';
        $stmt = $pdo->prepare("UPDATE announcements SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        $_SESSION['msg_success'] = "✅ Announcement status updated.";
        header("Location: admin_announcement.php");
        exit();
    } catch (PDOException $e) {
        die("Error toggling announcement: " . $e->getMessage());
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM announcements ORDER BY posted_at DESC");
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
<title>Admin Announcements | Student Parliament</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4 fw-bold">📢 Manage Announcements</h2>

    <?php
    if (!empty($_SESSION['msg_success'])) {
        echo '<div class="alert alert-success text-center">' . $_SESSION['msg_success'] . '</div>';
        unset($_SESSION['msg_success']);
    }
    ?>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="announcement_id" value="<?= $editMode ? htmlspecialchars($editAnnounce['id']) : '' ?>">
                <div class="mb-2">
                    <input type="text" name="title" class="form-control" placeholder="Title" required
                        value="<?= $editMode ? htmlspecialchars($editAnnounce['title']) : '' ?>">
                </div>
                <div class="mb-2">
                    <textarea name="content" class="form-control" placeholder="Content" rows="3" required><?= $editMode ? htmlspecialchars($editAnnounce['content']) : '' ?></textarea>
                </div>
                <div class="text-end">
                    <?php if ($editMode): ?>
                        <button type="submit" name="update_announcement" class="btn btn-warning">Update</button>
                        <a href="admin_announcement.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_announcement" class="btn btn-primary">Add Announcement</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Posted By</th>
                        <th>Posted At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($announcements) > 0): ?>
                        <?php foreach ($announcements as $a): ?>
                        <tr class="text-center">
                            <td><?= htmlspecialchars($a['id']); ?></td>
                            <td><?= htmlspecialchars($a['title']); ?></td>
                            <td><?= htmlspecialchars($a['posted_by']); ?></td>
                            <td><?= date('F d, Y h:i A', strtotime($a['posted_at'])); ?></td>
                            <td>
                                <?php if ($a['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?edit=<?= $a['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?delete=<?= $a['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this announcement?');">Delete</a>
                                <a href="?toggle=<?= $a['id']; ?>" class="btn btn-info btn-sm">
                                    <?= $a['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No announcements found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
    <!-- 🟢 Back to Dashboard -->
    <div class="text-center mt-4">
        <a href="student-dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
