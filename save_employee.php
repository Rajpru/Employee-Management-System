<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name']);
    $department   = intval($_POST['department_id']);
    $role         = intval($_POST['role_id']);
    $country      = intval($_POST['country_id']);
    $city         = intval($_POST['city_id']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);
    $created_by   = $_SESSION['user_id'];

    $errors = [];
    if ($name == '') $errors[] = "Name is required.";
    if ($email == '') $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (!preg_match('/^\d{10}$/', $phone)) $errors[] = "Phone must be 10 digits.";
    if ($department == 0 || $role == 0 || $country == 0 || $city == 0) $errors[] = "All dropdowns must be selected.";

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO employees (name, department_id, role_id, country_id, city_id, email, phone, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("siiiissi", $name, $department, $role, $country, $city, $email, $phone, $created_by);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Employee added successfully.";
            header("Location: employee_list.php");
            exit();
        } else {
            $_SESSION['error'] = "Error adding employee: " . $stmt->error;
            header("Location: add_employee.php");
            exit();
        }
    } else {
        $_SESSION['errors'] = $errors;
        header("Location: add_employee.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
