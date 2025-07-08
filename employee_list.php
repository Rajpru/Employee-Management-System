<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

$name_filter = $_GET['name'] ?? '';
$dept_filter = $_GET['department_id'] ?? '';
$role_filter = $_GET['role_id'] ?? '';

$departments = $conn->query("SELECT id, name FROM departments");
$roles = $conn->query("SELECT id, name FROM roles");

$query = "
SELECT e.id, e.name AS employee_name, e.email, e.phone,
       d.name AS department, r.name AS role,
       c1.name AS country, c2.name AS city,
       e.created_at
FROM employees e
LEFT JOIN departments d ON e.department_id = d.id
LEFT JOIN roles r ON e.role_id = r.id
LEFT JOIN countries c1 ON e.country_id = c1.id
LEFT JOIN cities c2 ON e.city_id = c2.id
WHERE 1=1
";

if ($name_filter) {
  $query .= " AND e.name LIKE '%" . $conn->real_escape_string($name_filter) . "%'";
}
if ($dept_filter) {
  $query .= " AND e.department_id = " . intval($dept_filter);
}
if ($role_filter) {
  $query .= " AND e.role_id = " . intval($role_filter);
}

$query .= " ORDER BY e.id DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
  <?php require 'sidebar.php'; ?>

  <div class="flex-grow-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Employee List</h3>
      <a href="add_employee.php" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Add Employee
      </a>
    </div>

    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="Search by name" value="<?= htmlspecialchars($name_filter) ?>">
      </div>
      <div class="col-md-3">
        <select name="department_id" class="form-select">
          <option value="">All Departments</option>
          <?php while($dept = $departments->fetch_assoc()): ?>
            <option value="<?= $dept['id'] ?>" <?= $dept_filter == $dept['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($dept['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3">
        <select name="role_id" class="form-select">
          <option value="">All Roles</option>
          <?php while($role = $roles->fetch_assoc()): ?>
            <option value="<?= $role['id'] ?>" <?= $role_filter == $role['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($role['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="employee_list.php" class="btn btn-secondary">Reset</a>
      </div>
    </form>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Department</th>
          <th>Role</th>
          <th>Country</th>
          <th>City</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['employee_name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><?= htmlspecialchars($row['department']) ?></td>
              <td><?= htmlspecialchars($row['role']) ?></td>
              <td><?= htmlspecialchars($row['country']) ?></td>
              <td><?= htmlspecialchars($row['city']) ?></td>
              <td><?= htmlspecialchars($row['created_at']) ?></td>
              <td>
                <a href="add_employee.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="delete_employee.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this employee?');">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="10">No employees found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
