<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script src="..\dependencies\Ajax\glm-ajax.js"></script>
	<title>admin_2.php</title>
</head>
<body>
	<input type="checkbox" id="veh1" name="vehicle1" value="Bike">
	<label for="vehicle1"> I have a bike</label><br>
	<input type="checkbox" id="veh2" name="vehicle2" value="Car">
	<label for="vehicle2"> I have a car</label><br>
	<input type="checkbox" id="veh3" name="vehicle3" value="Boat">
	<label for="vehicle3"> I have a boat</label><br> 
	
	<input type="submit" class="button" id="choices_btn" value="confirm"/>
</body>

<script type="text/JavaScript" defer>
	function send_choices()
	{
		// x[0] -> elem_id, x[1] -> ischecked?
		var x = [];
		x[0] = "veh1";
		x[1] = document.getElementById("veh1").checked;
		//console.log(x);

		var y = document.getElementById("veh2").checked;
		var z = document.getElementById("veh3").checked;
		
		console.log(x + " y " + y + " z " + z);
		
		console.log('sending choices...');
	}
	
	document.getElementById("choices_btn").onclick = send_choices;
</script>