<?php

session_start();

$path = $_SERVER["DOCUMENT_ROOT"] . '/front_end/config/db.php';
//echo($path);
require $path;

// TODO ------------
//Data is ready - all that remains is to form
//tables and serve to client

// a)
//$user_count = get_user_count($conn);

// b)
//$methods[<method>] -> <count>
//$methods = get_method_count($conn);

// c)
//$statuses[<status>] -> <count>
//$statuses = get_statuses($conn);

// d)
// $num_of_unique_domains -> count(<domains>)
//$num_of_unique_domains = get_unique_domains($conn);

// f)
// $avg_ages_by_cont_type[<content_type>] -> avg(<content_type>)
//$avg_ages_by_cont_type = get_avg_age_by_cnt_type($conn);



// ****************** Helper Functions ******************************/

//Get the count of users
function get_user_count(&$conn)
{
	//Prepare the statement
	$user_count = $conn->prepare("SELECT count(id) FROM users");
	//execute! (off-off-off with their heads?)
	$user_count->execute();
	$u_count = $user_count->get_result();
	$u_count = $u_count->fetch_assoc();
	//var_dump($u_count);

	$user_count->close();	

	return $u_count;	
}

// Get the count of rows(tuples) by type of method
function get_method_count(&$conn)
{
	$methods = null;

	//Prepare the statement
	$method_count = $conn->prepare("SELECT count(method), method FROM files WHERE method IS NOT NULL GROUP BY(method)");
	//execute! (off-off-off with their heads?)
	$method_count->execute();
	$method_count->bind_result($m1, $m2);

	while ($method_count->fetch())
	{
		//var_dump($m1, $m2);
		$methods[$m2] = $m1;
	}

	//var_dump($methods);

	$method_count->close();	

	return $methods;
}

// get count(status) for every status
function get_statuses(&$conn)
{
	$statuses = null;

	//Prepare the statement
	$statuses_count = $conn->prepare("SELECT count(status), status FROM files WHERE status IS NOT NULL GROUP BY(status)");
	//execute! (off-off-off with their heads?)
	$statuses_count->execute();
	$statuses_count->bind_result($s1, $s2);

	while ($statuses_count->fetch())
	{
		//var_dump($m1, $m2);
		$statuses[$s2] = $s1;
	}

	var_dump($statuses);

	$statuses_count->close();	

	return $statuses;

}

// get count of distinct domains
function get_unique_domains(&$conn)
{
	//Prepare the statement
	$domains_count = $conn->prepare("SELECT count(DISTINCT domain) FROM files WHERE domain IS NOT NULL");
	//execute! (off-off-off with their heads?)
	$domains_count->execute();
	$d_count = $domains_count->get_result();
	$d_count = $d_count->fetch_assoc();

	var_dump($d_count);

	$domains_count->close();	

	return $d_count;
}

// get avg age of web artifacts (I disregard artifacts with NULL age)
// by content_type
function get_avg_age_by_cnt_type(&$conn)
{
	$avg_ages = null;

	//Prepare the statement
	$avg_ages_q = $conn->prepare("SELECT content_type, avg(age) FROM files WHERE age IS NOT NULL GROUP BY (content_type)");
	//execute! (off-off-off with their heads?)
	$avg_ages_q->execute();

	$avg_ages_q->bind_result($ct, $aa);

	while ($avg_ages_q->fetch())
	{
		//var_dump($ct, $aa);
		$avg_ages[$ct] = $aa;
	}

	var_dump($avg_ages);

	$avg_ages_q->close();	

	return $avg_ages;
}

?>

<!-- dummys below -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!--Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" >
	<link rel="stylesheet" href="sudo.css">
	<link rel="stylesheet" href="sidebar.css">
	<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" ></script> -->
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>

	<title>Στατιστικά</title>
</head>

<body>
	<div>
		<h2>Admin satistics</h2>
	</div>
</body>