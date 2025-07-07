<?php
$current_page = basename($_SERVER['PHP_SELF']);
function isActive($page) {
    global $current_page;
    return $current_page === $page ? 'fw-bold active-link' : '';
}
function isArrowVisible($page) {
    global $current_page;
    return $current_page === $page ? '<span class="text-white">&raquo;</span>' : '<span></span>';
}
?>

<div class="bg-dark sidebar text-white vh-100 p-3 position-relative" style="min-width: 300px;" id="sidebar">
  <!-- Close Button -->
  <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 d-md-none" onclick="document.getElementById('sidebar').style.display='none'">
    &times;
  </button>

  <a href="index.php" class="d-block text-white text-decoration-none">
    <h5 class="text-center">Admin Panel</h5>
  </a>

  <a href="employee_list.php" class="d-flex justify-content-between align-items-center text-white py-2 px-3 text-decoration-none <?= isActive('employee_list.php') ?>">
    <span>Employees</span><?= isArrowVisible('employee_list.php') ?>
  </a>

  <a href="dept.php" class="d-flex justify-content-between align-items-center text-white py-2 px-3 text-decoration-none <?= isActive('dept.php') ?>">
    <span>Department</span><?= isArrowVisible('dept.php') ?>
  </a>

  <a href="role.php" class="d-flex justify-content-between align-items-center text-white py-2 px-3 text-decoration-none <?= isActive('role.php') ?>">
    <span>Role</span><?= isArrowVisible('role.php') ?>
  </a>

  <a href="logout.php" class="d-block text-danger py-2 px-3 text-decoration-none">
    Logout
  </a>
</div>

<style>
  .active-link {
    background-color: #0d6efd;
    border-radius: 5px;
  }
</style>
