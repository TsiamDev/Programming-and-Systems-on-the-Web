<?php

require_once 'config/db.php';

$sqlQuery = "SELECT content_type, max_age FROM files WHERE content_type IS NOT NULL AND max_age IS NOT NULL";

$result = mysqli_query($conn,$sqlQuery);

$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

mysqli_close($conn);

//this is a problem without HTTPS
//because it will send unecrypted data
echo json_encode($data);
?>