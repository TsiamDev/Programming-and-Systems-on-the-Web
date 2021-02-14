<?php

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>

</body>

<script type="text/JavaScript">
	$.get("http://api.ipstack.com/8.8.8.8?access_key=e18de63c569fffbe096ef6d116b6a2e2").always(function (resp) {
		console.log("response: ");
		console.log(resp);
	});
</script>