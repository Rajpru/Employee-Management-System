<?php
include 'db_connect.php';

if (isset($_POST['country_id'])) {
    $country_id = intval($_POST['country_id']);
    $stmt = $conn->prepare("SELECT id, name FROM cities WHERE country_id = ?");
    $stmt->bind_param("i", $country_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">-- Select City --</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="'.$row['id'].'">'.htmlspecialchars($row['name']).'</option>';
    }
    $stmt->close();
}
?>
