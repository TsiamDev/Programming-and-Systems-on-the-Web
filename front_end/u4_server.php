<?php

//echo "u4_server.php";

require_once 'config/db.php';

$sqlQuery = "SELECT content_type, serverIP FROM files WHERE ((content_type LIKE '%html%') OR (content_type LIKE '%php%') OR (content_type LIKE '%asp%') OR (content_type LIKE '%jsp%')) AND (serverIP IS NOT NULL)";

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