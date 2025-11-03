<?php
session_start();
include 'dbconnection.php';

if (isset($_POST['btnSubmit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: student-dashboard.php");
                } else {
                    header("Location: user-dashboard.php");
                }
                exit();
            } else {
                $_SESSION['msg'] = "Invalid password!";
            }
        } else {
            $_SESSION['msg'] = "Email not found!";
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = "Database error: " . $e->getMessage();
    }

    header("Location: login.php");
    exit();
}
?>