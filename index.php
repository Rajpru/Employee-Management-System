<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      overflow-x: hidden;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
  </style>
</head>
<body>

  <div class="d-flex">
    <?php require 'sidebar.php'; ?>

    <div class="flex-grow-1 p-4">
      <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>
      <p>Select a section from the menu to manage your data.</p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
