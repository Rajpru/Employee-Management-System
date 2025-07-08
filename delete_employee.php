<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_error'] = "Invalid employee ID.";
    header("Location: employee_list.php");
    exit();
}

include 'db_connect.php';

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = "Employee deleted successfully.";
} else {
    $_SESSION['flash_error'] = "Failed to delete employee.";
}

$stmt->close();
$conn->close();

header("Location: employee_list.php");
exit();
?>
