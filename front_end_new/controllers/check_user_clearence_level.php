<?php
	session_start();

	$path = $_SERVER["DOCUMENT_ROOT"] . '/front_end/config/db.php';
	//echo($path);
	require $path;
	
	//simple query to the db - to check the <type> field of the user_error
	$user_type = get_user_type($conn);
	echo $user_type . "\r\n";
	//redirect to correct html page
	if( $user_type != NULL )
	{
		//he is admin!
		$admin_path = 'ui_for_admin_1.php';
		header("Location: .$admin_path");
		die("expected shutdown");
	}else
	{
		//meh! regular user...
		$user_path = 'ui_for_user.php';
		echo $user_path;
		header("Location: $user_path");
		die("expected shutdown");
	}


	
	/******************************* Helper functions ****************************/
	
	//call by reference
	function get_user_type(&$conn)
	{
		//var_dump($_SESSION);
		//sanity check
		if( !isset($_SESSION['username']) )
		{
			die("username not set!");
		}
		
		//Get the user->id
		//Prepare the statement
		$_user_type = $conn->prepare("SELECT type FROM users WHERE username = ?");
		
		$_user_type->bind_param("s", $user_name);
		//Set the correct username to the bound variable
		//NOTE: getting it directly from the SESSION might be dangerous?
		$user_name = $_SESSION['username'];
		
		//execute! (off-off-off with their heads?)
		$_user_type->execute();
		$u_type = $_user_type->get_result();
		$u_type = $u_type->fetch_assoc();
		//var_dump($u_type);

		$_user_type->close();
	}	
?>

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
		<h2>90% done</h2>
	</div>
</body>