<?php
include('dbconnection.php');

try {
    // ✅ Total students
    $total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();

    // ✅ Total attendance (unique users who attended)
    $total_attendance = $pdo->query("
        SELECT COUNT(DISTINCT user_id) FROM attendance
        WHERE (am_in IS NOT NULL OR pm_in IS NOT NULL)
    ")->fetchColumn();

    // ✅ Total fines amount
    $total_fines = $pdo->query("SELECT COALESCE(SUM(total_fine),0) FROM fines")->fetchColumn();

    // ✅ Students per Department
    $students_by_dept = $pdo->query("
        SELECT department, COUNT(*) AS count
        FROM users
        WHERE role = 'student'
        GROUP BY department
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Attendance per Event
    $attendance_by_event = $pdo->query("
        SELECT e.event_name, COUNT(DISTINCT a.user_id) AS attendees
        FROM attendance a
        INNER JOIN institutional_events e ON a.event_id = e.id
        GROUP BY e.id
        ORDER BY e.id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Fines per Event
    $fines_by_event = $pdo->query("
        SELECT e.event_name, COALESCE(SUM(f.total_fine),0) AS total_fine
        FROM fines f
        INNER JOIN institutional_events e ON f.event_id = e.id
        GROUP BY e.id
        ORDER BY e.id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 🧾 Send JSON response
    echo json_encode([
        "total_students" => $total_students,
        "total_attendance" => $total_attendance,
        "total_fines" => number_format($total_fines, 2),
        "students_by_dept" => $students_by_dept,
        "attendance_by_event" => $attendance_by_event,
        "fines_by_event" => $fines_by_event
    ]);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
