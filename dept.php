<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

$success = $error = "";
$editing = false;
$edit_data = [
    'id' => '',
    'name' => ''
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $created_by = $_SESSION['user_id'];

    if (isset($_POST['dept_id']) && $_POST['dept_id'] !== '') {
        // Update department
        $id = intval($_POST['dept_id']);
        $stmt = $conn->prepare("UPDATE departments SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            $success = "Department updated successfully.";
        } else {
            $error = "Failed to update department.";
        }
        $stmt->close();
    } else {
        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO departments (name, created_by) VALUES (?, ?)");
            $stmt->bind_param("si", $name, $created_by);
            if ($stmt->execute()) {
                $_SESSION['flash_success'] = "Department added successfully.";
                header("Location: dept.php");
                exit();
            } else {
                $_SESSION['flash_error'] = "Failed to add department.";
                header("Location: dept.php");
                exit();
            }
            $stmt->close();
        } else {
            $error = "Department name is required.";
        }
    }
}
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        $edit_data = $result->fetch_assoc();
        $editing = true;
    }
    $stmt->close();
}
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dept.php");
    exit();
}
$query = "SELECT * FROM departments ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Department Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
  <?php require 'sidebar.php'; ?>

  <div class="flex-grow-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Department List</h3>
    </div>

    <!-- Alert Messages -->
    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Department Form -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="dept_id" value="<?= htmlspecialchars($edit_data['id']) ?>">
          <div class="mb-3">
            <label class="form-label">Department Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter Department Name" value="<?= htmlspecialchars($edit_data['name']) ?>" required>
          </div>
          <button type="submit" class="btn btn-primary">
            <?= $editing ? 'Update' : 'Add' ?> Department
          </button>
          <?php if ($editing): ?>
            <a href="dept.php" class="btn btn-secondary">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- Department Table -->
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Department Name</th>
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
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this department?');">
                  <i class="bi bi-trash"></i> Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">No departments found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
