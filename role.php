<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

$editing = false;
$edit_data = ['id' => '', 'name' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $created_by = $_SESSION['user_id'];

    if (isset($_POST['role_id']) && $_POST['role_id'] !== '') {
        $id = intval($_POST['role_id']);
        $stmt = $conn->prepare("UPDATE roles SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            $_SESSION['flash_success'] = "Role updated successfully.";
        } else {
            $_SESSION['flash_error'] = "Failed to update role.";
        }
        header("Location: role.php");
        exit();
    } else {
        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO roles (name, created_by) VALUES (?, ?)");
            $stmt->bind_param("si", $name, $created_by);
            if ($stmt->execute()) {
                $_SESSION['flash_success'] = "Role added successfully.";
            } else {
                $_SESSION['flash_error'] = "Failed to add role.";
            }
            header("Location: role.php");
            exit();
        } else {
            $_SESSION['flash_error'] = "Role name cannot be empty.";
            header("Location: role.php");
            exit();
        }
    }
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM roles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $edit_data = $res->fetch_assoc();
        $editing = true;
    }
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: role.php");
    exit();
}

$query = "SELECT * FROM roles ORDER BY id DESC";
$result = $conn->query($query);

$success = $_SESSION['flash_success'] ?? '';
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Role Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
  <?php require 'sidebar.php'; ?>

  <div class="flex-grow-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Role List</h3>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Form -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="role_id" value="<?= htmlspecialchars($edit_data['id']) ?>">
          <div class="mb-3">
            <label class="form-label">Role Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter Role Name" value="<?= htmlspecialchars($edit_data['name']) ?>" required>
          </div>
          <button type="submit" class="btn btn-primary">
            <?= $editing ? 'Update' : 'Add' ?> Role
          </button>
          <?php if ($editing): ?>
            <a href="role.php" class="btn btn-secondary">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Role Name</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['created_at']) ?></td>
              <td>
                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary me-2">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this role?');">
                  <i class="bi bi-trash"></i> Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">No roles found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
