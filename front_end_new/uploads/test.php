<?php

echo '0';
$key = '?access_key=e18de63c569fffbe096ef6d116b6a2e2';

$ch = curl_init("http://api.ipstack.com/8.8.8.8/" . $key);
//$fp = fopen("example_homepage.txt", "w");

//curl_setopt($ch, CURLOPT_FILE, $fp);
//curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
if(curl_error($ch)) {
    fwrite($fp, curl_error($ch));
}else{
	echo '1';
	var_dump($ch);
}
curl_close($ch);
fclose($fp);

?>
