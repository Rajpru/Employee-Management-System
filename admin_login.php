<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = trim($_POST["username"]);
    $pass = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_pass);
        $stmt->fetch();

        if (password_verify($pass, $hashed_pass)) {
            // Set session and redirect
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $user;
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Username not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
