<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = trim($_POST["username"]);
    $pass = $_POST["password"];
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $hashed_pass);

    if ($stmt->execute()) {
        header("Location: admin_login.php");
        exit();
    } else {
        $error = "Registration failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h4 class="text-center mb-3">Register</h4>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">User Name</label>
        <input type="text" class="form-control" name="username" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-success">Register</button>
      </div>

      <div class="mt-3 text-center">
        <a href="login.php">Back to Login</a>
      </div>
    </form>
  </div>

</body>
</html>
