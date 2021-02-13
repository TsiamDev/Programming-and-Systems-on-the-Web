<?php

session_start();

//Security check
// I expect at most 13 entries(arrays) in $_POST
$post_count = count($_POST);
if($post_count > 13)
{
	die("Unexpected POST");
}
// It could be suspicious if there are < 13 entries
// I have further restricions for that, later in this file

echo("1"); //Remove this

//open DB connection - I deliberately delayed this
//untill after the basic security check
$path = $_SERVER["DOCUMENT_ROOT"] . '/front_end/config/db.php';
//echo($path);
require $path;

// De-reference $_POST
// Post - mortem -> aaaaaall these could have been one object buuuuuttt....
// As mistakes, we make people.
// Also!
// I think this is faster than having to create an object and de-reference/cast it 
// every time I want to use a value.
// (I would de-reference/cast a lot! the rest of this file is proof!) 
// but I might be wrong - not sure how to measure time of execution
$methods = $_POST['methods'];
$serverIPs = $_POST['entriesServerIPAddress'];
$domains = $_POST['domains'];
$ages = $_POST['ages'];
$status = $_POST['status'];
$content_types = $_POST['content_types'];
$expires = $_POST['expires'];
$last_modified = $_POST['last_modified'];
$max_ages = $_POST['max_age'];
$cache_private = $_POST['cacheability_private'];
$cache_public = $_POST['cacheability_public'];
$cache_no_store = $_POST['cacheability_no_store'];
$cache_no_cache = $_POST['cacheability_no_cache'];

//var_dump($_POST);

// get the current user id ready
$user_id = get_user_id($conn);

// I have max 13 entries foreach web artifact
// the method is <theoretically> always provided
// so I use the count($methods) to... count how 
// many web artifacts exist in the uploaded file
for ($i = 0; $i < count($methods); $i++) { 

	//Variables for the MySQL prepared statements 
	//Clear on every iteration for good measure
	$method = null;
	$serverIP = null;
	$domain = null;
	$age = null;
	$this_status = null;
	$content_type = null;
	$this_expires = null;
	$this_last_modified = null;
	$max_age = null;
	$this_cache_private = null;
	$this_cache_public = null;
	$this_cache_no_store = null;
	$this_cache_no_cache = null;

	//echo 'new it';

	//method
	if (array_key_exists($i, $methods))
	{
		$method = $methods[$i];
		//echo $method;
	}

	//severIP
	if (array_key_exists($i, $serverIPs))
	{
		$serverIP = $serverIPs[$i];
	}

	//domain
	if (array_key_exists($i, $domains))
	{
		$domain = $domains[$i];
	}

	//age
	if (array_key_exists($i, $ages))
	{
		$age = $ages[$i];
	}

	//status
	if (array_key_exists($i, $status))
	{
		$this_status = $status[$i];
	}

	//content_type
	if (array_key_exists($i, $content_types))
	{
		$content_type = $content_types[$i];
	}

	//expires
	if (array_key_exists($i, $expires))
	{
		$this_expires = $expires[$i];
	}

	//last-modified
	if (array_key_exists($i, $last_modified))
	{
		$this_last_modified = $last_modified[$i];
	}

	//max-age
	if (array_key_exists($i, $max_ages))
	{
		$max_age = $max_ages[$i];
	}

	//caheability public
	if (array_key_exists($i, $cache_public))
	{
		$this_cache_public = $cache_public[$i];
	}

	//cacheability private
	if (array_key_exists($i, $cache_private))
	{
		$this_cache_private = $cache_private[$i];
	}

	//cacheability no-cache
	if (array_key_exists($i, $cache_no_cache))
	{
		$this_cache_no_cache = $cache_no_cache[$i];
	}

	//cacheability no-store
	if (array_key_exists($i, $cache_no_store))
	{
		$this_cache_no_store = $cache_no_store[$i];
	}

	/*
	var_dump($method, $serverIP, $domain, $age, $this_status, $content_type, $this_expires, $this_last_modified, $max_age, 
		$this_cache_private, $this_cache_public, $this_cache_no_store, 
		$this_cache_no_cache);
	*/
	//Prepare & execute the insert
	prepare_insert($user_id, $conn, $method, $serverIP, $domain, $age, $this_status, $content_type, $this_expires, $this_last_modified, $max_age, 
		$this_cache_private, $this_cache_public, $this_cache_no_store, 
		$this_cache_no_cache);
}

// ************************ Helper functions below this point ***********************//

//call by reference
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

	return $user_id;
}	


function prepare_insert($arg_uid, &$conn, &$method, &$serverIP, &$domain, &$age, &$this_status, &$content_type, &$this_expires, &$this_last_modified, &$max_age, 
		&$this_cache_private, &$this_cache_public, &$this_cache_no_store, 
		&$this_cache_no_cache)
{
	//Prepare the insert
	//echo 'error list';
	//var_dump($conn->error_list);
	$insert_stmt = $conn->prepare("INSERT INTO files (Id, arch_id, name, size, downloads, method, serverIP, domain, age, status, content_type, expires, 
		last_modified, max_age, cache_private, cache_public, 
		cache_no_store, cache_no_cache) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	//echo 'error list';
	//var_dump($conn->error_list);

	//bind variables
	$insert_stmt->bind_param("iisiisssssssssiiii", $Id, $arch_id, $name, $size, $downloads, 
		$_method, $_serverIP, $_domain, $_age, $_this_status, $_content_type, $_this_expires, 
		$_this_last_modified, $_max_age, $_this_cache_private, $_this_cache_public, 
		$_this_cache_no_store, $_this_cache_no_cache);
	
	//set bound variable's values
	//user id - Foreign Key
	$Id = $arg_uid;

	//THESE ARE GENERIC - CHANGE!
	$arch_id = 0;
	$name = 'my first file!';
	$size = 65;
	$downloads = 5;

	//har data
	$_method = $method;
	$_serverIP = $serverIP;
	$_domain = $domain;
	$_age = $age;
	$_this_status = $this_status;
	$_content_type = $content_type;
	$_this_expires = $this_expires;
	$_this_last_modified = $this_last_modified;
	$_max_age = $max_age;
	$_this_cache_private = $this_cache_private;
	$_this_cache_public = $this_cache_public;
	$_this_cache_no_store = $this_cache_no_store;
	$_this_cache_no_cache = $this_cache_no_cache;

	//execute...
	$insert_stmt->execute();
	//close
	$insert_stmt->close();
}

//returns true if all arrays have numeric keys
//otherwise returns false (including the case something weird happened)
function check_if_any_assoc_arr(&$methods, &$serverIPs, &$domains, &$ages, &$status, &$content_types, &$expires, &$last_modified, &$max_ages, &$cache_private, &$cache_public, 
	&$cache_no_store, &$cache_no_cache)
{
	$ret = is_associative($methods) * is_associative($serverIPs) * is_associative($domains) * is_associative($ages) * is_associative($status) * is_associative($content_types) *is_associative($expires) * is_associative($last_modified) * is_associative($max_ages) * is_associative($cache_private) * is_associative($cache_public) * is_associative($cache_no_store) * is_associative($cache_no_cache);
	if($ret === 1)
	{
		//none are associative
		return true;
	}else
	{
		//either something went wrong or at least one is associative
		return false;
	}
}

//Based on: https://www.geeksforgeeks.org/how-to-check-an-array-is-associative-or-sequential-in-php/
//Basically, if there is at least one string key
//then php regards the array as associative 
//which might be an indication of suspicious activity, 
//as the expected arrays should have only numeric keys
function is_associative(array &$array) 
{
	$string_keys_num = count(array_filter(array_keys($_POST['methods']), 'is_string'));
	if( $string_keys_num > 0 )
	{
		return 0;
	}else if ( $string_keys_num === 0)
	{
		return 1;
	}else 
	{
		// something went wrong somewhere
		return -1;
	}
}