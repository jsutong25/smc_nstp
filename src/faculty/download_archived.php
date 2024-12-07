<?php
include '../connect.php';

$user_ids = $_GET['user_ids'];
$ids = implode(',', array_map('intval', explode(',', $user_ids)));

$query = "SELECT * FROM user WHERE user_id IN ($ids)";
$result = mysqli_query($conn, $query);

header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=archived_users.xls");

echo "ID\tName\tEmail\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['id'] . "\t" . $row['name'] . "\t" . $row['email'] . "\n";
}
?>
