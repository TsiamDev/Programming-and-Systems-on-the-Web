<?php

require_once 'config/db.php';

$sqlQuery = "SELECT content_type, max_age, expires FROM files ";

$result = mysqli_query($conn,$sqlQuery);

$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

mysqli_close($conn);

echo json_encode($data);
?>
