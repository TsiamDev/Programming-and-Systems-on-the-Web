<?php

session_start();

//receptacle for storing the client ip
$client_IP = null;

// As I found, client ip could be in one of these fields
if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
{
	//'sanity' check
	if (gettype($_SERVER['HTTP_X_FORWARDED_FOR']) !== "string")
	{
		die("unexpected field");
	}

	echo 'http_x_forwarded_for is set, ip: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];
	$client_IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else if ( isset($_SERVER['REMOTE_ADDR']) )
{
	//'sanity' check
	if (gettype($_SERVER['REMOTE_ADDR']) !== "string")
	{
		die("unexpected field");
	}
	echo 'remote_addr is set, ip: ' . $_SERVER['REMOTE_ADDR'];
	$client_IP = $_SERVER['REMOTE_ADDR'];
}

//Security check
// I expect at most 16 entries(arrays) in $_POST
$post_count = count($_POST);
if($post_count > 16)
{
	die("Unexpected POST");
}
// It could be suspicious if there are < 13 entries
// I have further restricions for that, later in this file

//echo("1");

//open DB connection - I deliberately delayed this
//untill after the basic security check
//the require below, also gets the constants.php 
//(I also use the ipstack access key, which exists 
//in the constants.php - It is aaaaal part of the master plan ^^)
$path = $_SERVER["DOCUMENT_ROOT"];
//echo($path);
require $path . '/front_end/config/db.php';

//echo IPSTACK_KEY;

// De-reference $_POST
// NOTICE:
// Post - mortem -> aaaaaall these could have been one object buuuuuttt....
// As mistakes, we make people.
// NOTICE #2:
// I think this is faster than having to create an object and de-reference/cast it 
// every time I want to use a value.
// (I would de-reference/cast a lot! the rest of this file is proof!) 
$methods = check_if_isset('methods');
$serverIPs = check_if_isset('entriesServerIPAddress');
$domains = check_if_isset('domains');
$ages = check_if_isset('ages');
$status = check_if_isset('status');
$content_types = check_if_isset('content_types');
$req_max_stales = check_if_isset('req_max_stales');
$req_min_freshs = check_if_isset('req_min_freshs');
$expires = check_if_isset('expires');
$last_modified = check_if_isset('last_modified');
$max_ages = check_if_isset('max_age');
$cache_private = check_if_isset('cacheability_private');
$cache_public = check_if_isset('cacheability_public');
$cache_no_store = check_if_isset('cacheability_no_store');
$cache_no_cache = check_if_isset('cacheability_no_cache');



//var_dump($_POST);

// get the current user id ready
$user_id = get_user_id($conn);

insert_user_ip($conn, $client_IP);

// I have max 13 entries foreach web artifact
// the method is <theoretically> always provided
// so I use the count($methods) to... count how 
// many web artifacts exist in the uploaded file
for ($i = 0; $i < count($methods); $i++) { 

	//Variables for the MySQL prepared statements 
	//Clear on every iteration for good measure
	$method = null;
	$serverIP = null;
	$server_lat = null;
	$server_long = null;
	$domain = null;
	$age = null;
	$this_status = null;
	$content_type = null;
	$req_content_type = null;
	$req_max_stale = null;
	$req_min_fresh = null;
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
		
		//get the serverIP geolocation
		//$ret = get_server_geo_data($serverIP);
		$ret = NULL;
		
		//check happens WITH type juggling
		// '===' is WITHOUT
		if($ret == NULL)
		{
			echo 'could not retrieve geolocation data, but will add to db' . "\r\n";
		}else
		{
			echo 'server geo data successfully retrieved ' . "\r\n";
			$server_lat = $ret[0];
			$server_long = $ret[1];
		}
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
	
	//req_content_type
	if (array_key_exists($i, $req_content_types))
	{
		$req_content_type = $req_content_types[$i];
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
	
	//max-stale
	if (array_key_exists($i, $req_max_stales))
	{
		$req_max_stale = $req_max_stales[$i];
	}
	
	//min-fresh
	if (array_key_exists($i, $req_min_freshs))
	{
		$req_min_fresh = $req_min_freshs[$i];
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
	prepare_insert($user_id, $conn, $method, $serverIP, $server_lat, $server_long, $domain, $age, $this_status, $content_type, $req_content_type, $this_expires, $this_last_modified, $max_age, 
		$this_cache_private, $this_cache_public, $this_cache_no_store, 
		$this_cache_no_cache, $req_max_stale, $req_min_fresh);
}

// ************************ Helper functions below this point ***********************//

//checks whether the specific field exists in $_POST
function check_if_isset(str_key)
{
	if( isset( $_POST[str_key] ) )
	{
		return $_POST[str_key];
	}else
	{
		return null;
	}
}

//this function does not "do" anything (see comments below)
//it is just here as a placeholder
function insert_user_ip(&$conn, &$client_IP)
{
	echo 'retriveing isp data';
	
	/*
	if( $client_IP=== NULL )
	{
		echo 'client ip was null - cannot get geo data' . "\r\n";
		return NULL;
	}
	
	// NOTICE:
	// all the "users" of our website will be using localhost,
	// so this call will return null values (I tested it, see 
	// report) so we propably won't have any 'provider' data.
	// Nevertheless, this works properly with regular IPs
	// (because I won't be able to demonstrate this, I 
	// put here "barebones code" only. See below, get_server_geo_data()
	// for functional code)
	// NOTICE #2:
	// I just noticed ipstack 'free version' does not provide isp info.. 
	$ch = curl_init("http://api.ipstack.com/" . $client_IP . IPSTACK_KEY);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 4);
	
	//get the ipstack response
	$res = curl_exec($ch);
	
	//check if any errors happened
	if(curl_error($ch)) {
		echo curl_error($ch);
	}
	//RELEASE THE... curl-ACKEN?
	curl_close($ch);
	
	$res = json_decode($res);
	//$res -> connection -> isp will have the required data
	//so just insert to db to <ips> table along with the
	//$user_id as foreign key. Basic stuff
	*/
}

function get_server_geo_data($server_ip)
{
	//echo 'retriveing data';
	//execution time measure
	//$init = microtime(true);
	if( $server_ip === NULL )
	{
		echo 'server ip was null - cannot get geo data' . "\r\n";
		return NULL;
	}
	
	$ch = curl_init("http://api.ipstack.com/" . $server_ip . IPSTACK_KEY);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 4);
	
	//get the ipstack response
	$res = curl_exec($ch);
	
	//check if any errors happened
	if(curl_error($ch)) {
		echo curl_error($ch);
	}
	//RELEASE THE... curl-ACKEN?
	curl_close($ch);
	
	$res = json_decode($res);
	//var_dump($res);
	//echo "res.latitude " . $res->latitude;
	//echo "\r\n";
	//echo "res.long " . $res->longitude;
	$geo = [];
	$geo[] = $res->latitude;
	$geo[] = $res->longitude;
	
	//$t = microtime(true) - $init;
	//echo "\r\n" . $t . " time passed" . "\r\n";
	
	return $geo;
}

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
	
	if( $user_id == NULL )
	{
		die("user not found?");
	}

	return $user_id;
}	


function prepare_insert($arg_uid, &$conn, &$method, &$serverIP, &$server_lat, &$server_long, &$domain, &$age, &$this_status, &$content_type,
		&$req_content_type, &$this_expires, &$this_last_modified, &$max_age, &$this_cache_private, &$this_cache_public, &$this_cache_no_store, 
		&$this_cache_no_cache, &$req_max_stale, &$req_min_fresh)
{
	//Prepare the insert
	//echo 'error list';
	//var_dump($conn->error_list);
	$insert_stmt = $conn->prepare("INSERT INTO files (Id, arch_id, name, size, downloads, method, serverIP, server_lat, server_long, domain, age, status, content_type, req_content_type,
		expires, last_modified, max_age, cache_private, cache_public, cache_no_store, cache_no_cache, upload_date, req_max_stale, req_min_fresh)
		VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	//echo 'error list';
	//var_dump($conn->error_list);

	//bind variables
	// s -> string
	// d -> non integer
	// i -> integer
	$insert_stmt->bind_param("iisiissddssssssssiiiisii", $Id, $arch_id, $name, $size, $downloads, 
		$_method, $_serverIP, $_server_lat, $_server_long, $_domain, $_age, $_this_status, $_content_type, $_req_content_type,
		$_this_expires, $_this_last_modified, $_max_age, $_this_cache_private, $_this_cache_public,	$_this_cache_no_store, 
		$_this_cache_no_cache, $_date, $_req_max_stale, $_req_min_fresh);
	
	//set bound variable's values
	//user id - Foreign Key
	$Id = $arg_uid;

	//THESE ARE GENERIC - CHANGE!
	$arch_id = 0;
	$name = 'my first file!';
	$size = 65;
	$downloads = 5;

	// current date
	$_date =date("Y-m-d");

	//har data
	$_method = $method;
	$_serverIP = $serverIP;
	$_server_lat = $server_lat;
	$_server_long = $server_long;
	$_domain = $domain;
	$_age = $age;
	$_this_status = $this_status;
	$_content_type = $content_type;
	$_req_content_type = $req_content_type;
	$_this_expires = $this_expires;
	$_this_last_modified = $this_last_modified;
	$_max_age = $max_age;
	$_req_max_stale = $req_max_stale;
	$_req_min_fresh = $req_min_fresh;
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