<?php
	session_start();

	$path = $_SERVER["DOCUMENT_ROOT"] . '/front_end/config/db.php';
	//echo($path);
	require $path;
	
	//sanity check
	if( !isset($_SESSION['username']) )
	{
		die("username not set!");
	}
	
	//get file count for this user
	$file_count = get_file_count($conn);
	echo 'file' . "\r\n";
	var_dump($file_count);
	
	//get the last upload date for this user
	$last_date = get_last_upload_date($conn);
	var_dump($last_date['upload_date']);

	/****************** Helper functions ************************/
	
	function get_user_id(&$conn)
	{
		//Get the user->id
		//Prepare the statement
		$get_uid = $conn->prepare("SELECT id FROM users WHERE username = ?");
		$get_uid->bind_param("s", $user_name);
		//Set the correct username to the bound variable
		$user_name = $_SESSION['username'];
		//execute! (off-off-off with their heads?)
		$get_uid->execute();
		$user_id = $get_uid->get_result();
		//echo "uid: ";
		//var_dump($user_id);
		$user_id = $user_id->fetch_assoc();
		//var_dump($user_id);

		$get_uid->close();	
		
		if( $user_id == NULL )
		{
			die("user not found?");
		}

		return $user_id;
	}	
	
	function get_last_upload_date(&$conn)
	{
		
		//Prepare the statement
		$_date = $conn->prepare("SELECT upload_date FROM files INNER JOIN users ON files.Id = ? ORDER BY upload_date DESC LIMIT 1");
		$_date->bind_param("i", $u_id);
		
		$u_id = get_user_id($conn);	
		
		//execute! (off-off-off with their heads?)
		$_date->execute();
		$date = $_date->get_result();
		$date = $date->fetch_assoc();
		//var_dump($count);

		$_date->close();	
		
		return $date;
	}
	
	function get_file_count(&$conn)
	{
		//Prepare the statement
		$_count = $conn->prepare("SELECT COUNT(*) FROM files INNER JOIN users ON files.Id = users.id");
		//execute! (off-off-off with their heads?)
		$_count->execute();
		$count = $_count->get_result();
		$count = $count->fetch_assoc();
		//var_dump($count);

		$_count->close();	
		
		return $count;

	}
?>

