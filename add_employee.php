<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

$departments = $conn->query("SELECT id, name FROM departments");
$roles = $conn->query("SELECT id, name FROM roles");
$countries = $conn->query("SELECT id, name FROM countries");

$editMode = false;
$employee = [
    'id' => '',
    'name' => '',
    'email' => '',
    'phone' => '',
    'department_id' => '',
    'role_id' => '',
    'country_id' => '',
    'city_id' => '',
];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editMode = true;
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    $stmt->close();

    $cities = $conn->query("SELECT id, name FROM cities WHERE country_id = " . intval($employee['country_id']));
} else {
    $cities = false;
}

$success = $_SESSION['flash_success'] ?? '';
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $editMode ? 'Edit' : 'Add' ?> Employee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-header">
      <h4 class="mb-0"><?= $editMode ? 'Edit' : 'Add New' ?> Employee</h4>
    </div>
    <div class="card-body">
      <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

      <form method="POST" action="save_employee.php" onsubmit="return validateForm();">
        <?php if ($editMode): ?>
          <input type="hidden" name="id" value="<?= $employee['id'] ?>">
        <?php endif; ?>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Employee Name</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($employee['name']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($employee['email']) ?>">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($employee['phone']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Department</label>
            <select name="department_id" class="form-select select2" required>
              <option value="">-- Select Department --</option>
              <?php while($row = $departments->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($row['id'] == $employee['department_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($row['name']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Role</label>
            <select name="role_id" class="form-select select2" required>
              <option value="">-- Select Role --</option>
              <?php while($row = $roles->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($row['id'] == $employee['role_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($row['name']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Country</label>
            <select name="country_id" id="country" class="form-select select2" required>
              <option value="">-- Select Country --</option>
              <?php while($row = $countries->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($row['id'] == $employee['country_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($row['name']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">City</label>
          <select name="city_id" id="city" class="form-select select2" required>
            <option value="">-- Select City --</option>
            <?php if ($cities): while($row = $cities->fetch_assoc()): ?>
              <option value="<?= $row['id'] ?>" <?= ($row['id'] == $employee['city_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['name']) ?>
              </option>
            <?php endwhile; endif; ?>
          </select>
        </div>

        <button type="submit" class="btn btn-primary"><?= $editMode ? 'Update' : 'Save' ?> Employee</button>
        <a href="employee_list.php" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $('.select2').select2();

  $('#country').on('change', function() {
    const countryId = $(this).val();
    $.ajax({
      url: 'get_cities.php',
      method: 'POST',
      data: { country_id: countryId },
      success: function(data) {
        $('#city').html(data);
      }
    });
  });

  function validateForm() {
    const name = document.querySelector('[name="name"]').value.trim();
    const email = document.querySelector('[name="email"]').value.trim();
    const phone = document.querySelector('[name="phone"]').value.trim();
    const dept = document.querySelector('[name="department_id"]').value;
    const role = document.querySelector('[name="role_id"]').value;
    const country = document.querySelector('[name="country_id"]').value;
    const city = document.querySelector('[name="city_id"]').value;

    if (name.length < 2) {
      alert("Please enter a valid employee name (min 2 characters).");
      return false;
    }

    const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if (!emailPattern.test(email)) {
      alert("Please enter a valid email address.");
      return false;
    }

    const phonePattern = /^[6-9]\d{9}$/;
    if (!phonePattern.test(phone)) {
      alert("Please enter a valid 10-digit phone number.");
      return false;
    }

    if (!dept || !role || !country || !city) {
      alert("Please select Department, Role, Country, and City.");
      return false;
    }

    return true;
  }
</script>

</body>
</html>
