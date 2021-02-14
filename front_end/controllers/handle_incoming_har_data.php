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

//var_dump($_POST);

// De-reference $_POST
//wrapper object
$obj = new stdClass();

$obj->methods = check_if_isset('methods');
//var_dump($obj->methods);
$obj->serverIPs = check_if_isset('entriesServerIPAddress');
$obj->domains = check_if_isset('domains');
$obj->ages = check_if_isset('ages');
$obj->status = check_if_isset('status');
$obj->content_types = check_if_isset('content_types');
$obj->req_content_types = check_if_isset('req_content_types');
$obj->req_max_stales = check_if_isset('req_max_stales');
$obj->req_min_freshs = check_if_isset('req_min_freshs');
$obj->expires = check_if_isset('expires');
$obj->last_modified = check_if_isset('last_modified');
$obj->max_ages = check_if_isset('max_age');
$obj->cache_private = check_if_isset('cacheability_private');
$obj->cache_public = check_if_isset('cacheability_public');
$obj->cache_no_store = check_if_isset('cacheability_no_store');
$obj->cache_no_cache = check_if_isset('cacheability_no_cache');


echo 'done with checks' . "\r\n";
//var_dump($_POST);

// get the current user id ready
$user_id = get_user_id($conn);

insert_user_ip($conn, $client_IP);

// I have max 13 entries foreach web artifact
// the method is <theoretically> always provided
// so I use the count($methods) to... count how 
// many web artifacts exist in the uploaded file
for ($i = 0; $i < count($obj->methods); $i++) { 

	//wrapper object for the MySQL prepared statements 
	//clear on every iteration for good measure
	$obj2 = new stdClass();
	$obj2->method = check_if_key_exists($i, $obj->methods);
	var_dump($obj2->method);
	//var_dump($obj2->serverIP);
	$obj2->serverIP = check_if_key_exists($i, $obj->serverIPs);
	//var_dump($obj2->serverIP);
	//$ret = get_server_geo_data($serverIP);
	$ret = NULL;	//remove this
	
	//check happens WITH type juggling
	// '===' is WITHOUT
	if($ret == NULL)
	{
		echo 'could not retrieve geolocation data, but will add to db' . "\r\n";
		$obj2->server_lat = null;
		$obj2->server_long = null;
	}else
	{
		echo 'server geo data successfully retrieved ' . "\r\n";
		$obj2->server_lat = $ret[0];
		$obj2->server_long = $ret[1];
	}
	//$obj2->server_lat = check_if_key_exists($i, $obj->server_lat);
	//$obj2->server_long = null;
	$obj2->domain = check_if_key_exists($i, $obj->domains);
	$obj2->age = check_if_key_exists($i, $obj->ages);
	$obj2->this_status = check_if_key_exists($i, $obj->status);
	$obj2->content_type = check_if_key_exists($i, $obj->content_types);
	$obj2->req_content_type = check_if_key_exists($i, $obj->req_content_types);
	$obj2->req_max_stale = check_if_key_exists($i, $obj->req_max_stales);
	$obj2->req_min_fresh = check_if_key_exists($i, $obj->req_min_freshs);
	$obj2->this_expires = check_if_key_exists($i, $obj->expires);
	$obj2->this_last_modified = check_if_key_exists($i, $obj->last_modified);
	$obj2->max_age = check_if_key_exists($i, $obj->max_ages);
	$obj2->this_cache_private = check_if_key_exists($i, $obj->cache_private);
	$obj2->this_cache_public = check_if_key_exists($i, $obj->cache_public);
	$obj2->this_cache_no_store = check_if_key_exists($i, $obj->cache_no_store);
	$obj2->this_cache_no_cache = check_if_key_exists($i, $obj->cache_no_cache);

	echo 'new it';

	/*
	var_dump($method, $serverIP, $domain, $age, $this_status, $content_type, $this_expires, $this_last_modified, $max_age, 
		$this_cache_private, $this_cache_public, $this_cache_no_store, 
		$this_cache_no_cache);
	*/
	//Prepare & execute the insert
	prepare_insert($user_id, $conn, $obj2);
	/*
	prepare_insert($user_id, $conn, $method, $serverIP, $server_lat, $server_long, $domain, $age, $this_status, $content_type, $req_content_type, $this_expires, $this_last_modified, $max_age, 
		$this_cache_private, $this_cache_public, $this_cache_no_store, 
		$this_cache_no_cache, $req_max_stale, $req_min_fresh);
	*/
}

// ************************ Helper functions below this point ***********************//

//checks if <i> exists as a key in <array>
function check_if_key_exists($i, &$array)
{
	//echo 'if_key' . "\r\n";
	//var_dump($array);
	if( !is_array( $array ) )
	{
		return $array;
	}
	if (array_key_exists($i, $array))
	{
		//echo $array[$i];
		return $array[$i];
	}else
	{
		return null;
	}
}

//checks whether the specific field exists in $_POST
function check_if_isset($str_key)
{
	//echo '1';
	//var_dump ( !isset( $_POST[$str_key] ) );
	if (!isset( $_POST[$str_key] ))
	{
		return null;
	}

	//echo '2';
	//var_dump ( is_associative($str_key) );
	if ( is_associative($str_key) === 1)
	{
		return $_POST[$str_key];
	}

	//if you got here it is unsafe!
	die("unexpected values");
	return null; //just to be safe xD

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

/*
function prepare_insert($arg_uid, &$conn, &$method, &$serverIP, &$server_lat, &$server_long, &$domain, &$age, &$this_status, &$content_type,
		&$req_content_type, &$this_expires, &$this_last_modified, &$max_age, &$this_cache_private, &$this_cache_public, &$this_cache_no_store, 
		&$this_cache_no_cache, &$req_max_stale, &$req_min_fresh)
*/
function prepare_insert($arg_uid, &$conn, &$obj)
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
	$_method = $obj->method;
	$_serverIP = $obj->serverIP;
	$_server_lat = $obj->server_lat;
	$_server_long = $obj->server_long;
	$_domain = $obj->domain;
	$_age = $obj->age;
	$_this_status = $obj->this_status;
	$_content_type = $obj->content_type;
	$_req_content_type = $obj->req_content_type;
	$_this_expires = $obj->this_expires;
	$_this_last_modified = $obj->this_last_modified;
	$_max_age = $obj->max_age;
	$_req_max_stale = $obj->req_max_stale;
	$_req_min_fresh = $obj->req_min_fresh;
	$_this_cache_private = $obj->this_cache_private;
	$_this_cache_public = $obj->this_cache_public;
	$_this_cache_no_store = $obj->this_cache_no_store;
	$_this_cache_no_cache = $obj->this_cache_no_cache;

	//execute...
	$insert_stmt->execute();
	//close
	$insert_stmt->close();
}

//Based on: https://www.geeksforgeeks.org/how-to-check-an-array-is-associative-or-sequential-in-php/
//Basically, if there is at least one string key
//then php regards the array as associative 
//which might be an indication of suspicious activity, 
//as the expected arrays should have only numeric keys
function is_associative($str_key) 
{
	$string_keys_num = count(array_filter(array_keys($_POST[$str_key]), 'is_string'));
	if( $string_keys_num > 0 )
	{
		// associative array! not safe!
		return 0;
	}else if ( $string_keys_num === 0)
	{
		// array has only integer keys! safe!
		return 1;
	}else 
	{
		// something went wrong somewhere. definately not safe
		return -1;
	}
}