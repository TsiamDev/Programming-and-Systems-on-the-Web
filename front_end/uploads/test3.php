<?php
	require '../config/db.php';

	if( isset($_POST["ip"]) )
	{
		echo "ip received: " . $_POST["ip"];
		//die("ip received");
	}else
	{
		die("bad ip");
	}

	$ch = curl_init("http://api.ipstack.com/" . $_POST["ip"] . IPSTACK_KEY);
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
	echo "res.latitude " . $res->latitude;
	echo "\r\n";
	echo "res.long " . $res->longitude;
	
?>
