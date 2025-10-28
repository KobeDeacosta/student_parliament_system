<?php
include('../dbconnection.php');

$department = isset($_GET['department']) ? trim($_GET['department']) : '';

try {
    // ✅ Base query for attendance records (grouped per user, event, and date)
    $query = "
        SELECT 
            u.username AS name,
            u.department,
            ie.event_name,
            DATE(a.date) AS attendance_date,
            MAX(a.am_in)  AS am_in,
            MAX(a.am_out) AS am_out,
            MAX(a.pm_in)  AS pm_in,
            MAX(a.pm_out) AS pm_out
        FROM attendance a
        INNER JOIN users u ON a.user_id = u.id
        INNER JOIN institutional_events ie ON a.event_id = ie.id
    ";

    // ✅ Filter by department if not "All" or empty
    if ($department !== '' && $department !== 'All') {
        $query .= " WHERE u.department = :department";
    }

    $query .= "
        GROUP BY u.id, ie.id, DATE(a.date)
        ORDER BY attendance_date DESC
    ";

    $stmt = $pdo->prepare($query);

    if ($department !== '' && $department !== 'All') {
        $stmt->bindParam(':department', $department, PDO::PARAM_STR);
    }

    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo "<tr><td colspan='8' class='text-center text-muted'>No attendance records found.</td></tr>";
        exit;
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $am_in  = $row['am_in']  ? date('h:i A', strtotime($row['am_in'])) : '-';
        $am_out = $row['am_out'] ? date('h:i A', strtotime($row['am_out'])) : '-';
        $pm_in  = $row['pm_in']  ? date('h:i A', strtotime($row['pm_in'])) : '-';
        $pm_out = $row['pm_out'] ? date('h:i A', strtotime($row['pm_out'])) : '-';
        $date   = $row['attendance_date'] ? date('M d, Y', strtotime($row['attendance_date'])) : '-';

        echo "
        <tr>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['department']) . "</td>
            <td>" . htmlspecialchars($row['event_name']) . "</td>
            <td>$am_in</td>
            <td>$am_out</td>
            <td>$pm_in</td>
            <td>$pm_out</td>
            <td>$date</td>
        </tr>";
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='8' class='text-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
