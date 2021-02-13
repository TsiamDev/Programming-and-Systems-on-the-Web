<?php include 'filesLogic.php';?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!--Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" >
	<link rel="stylesheet" href="sudo.css">
	<link rel="stylesheet" href="sidebar.css">
	<script src="ll.js"></script>
	<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" ></script> -->
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>

	<title>Upload har</title>
	
</head>
<body>

	<div id="mySidebar" class="sidebar">
  		<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
 		 <a href="index.php">Ηοme</a>
 		 <a href="upload.php">Upload .har</a>
	</div>

	<div id="main">
  		<button class="openbtn" onclick="openNav()">&#9776; Μενού</button>
	</div>

	<div class="container">
		<div class="row">
			<div class="col-md-4 offset-md-4 form-div login">

				<?php if(count($signals) > 0): ?>
						<div class="alert alert-danger">
							<?php foreach( $signals as $signal): ?>
						 		<li><?php echo $signal; ?></li>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if(count($warns) > 0): ?>
						<div class="alert alert-success">
							<?php foreach( $warns as $warn): ?>
						 		<li><?php echo $warn; ?></li>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

				<div class="reveal-text" id = '110'>
					<h1>Ανέβασε και ανάλυσε http αρχεία.</h1>
				</div>

				<div class="up">
					<h5>Το αρχέιο πρέπει να έχει επέκταση .har . Μέγιστο μέγεθος αρχείου 10MB.</h5>
				</div>

				<form id="submit_form" enctype="multipart/form-data" >
         			
         			 <input type="file" name="myfile" id="har_file"> 
         			 <button type="submit" class="btn btn-primary btn-lg btn-block" name="save">upload</button>
       			 </form>
			</div>
		</div>
	</div>

	<div class="dropdown">
  				<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    			Διαχείρηση Λογαριασμού
  				</button>
  				<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
  							<button class="dropdown-item" type="button">Στατιστικά</button>
    					    <button class="dropdown-item" type="button"><a href="manage.php">Αλλαγή password</button>
    						<button class="dropdown-item" type="button"><a href="manage2.php">Αλλαγή username</button>
    						<div class="dropdown-divider"></div>
    						<button class="dropdown-item" type="button"><a href="index.php?logout=1">Αποσύνδεση</button>
  				</div>
			</div>	

</body>
</html>

<script type="text/JavaScript" defer> 

//let submit_har_button = document.querySelector('#sub_btn');
//submit_har_button.


/*
const serialize_form = form => JSON.stringify(
  Array.from(new FormData(form).entries())
       .reduce((m, [ key, value ]) => Object.assign(m, { [key]: value }), {})
);
*/
$('#submit_form').on('submit', function (event) {
	// Prevent the default action for the forms' (cancelable) event: submit
	//console.log(event);
	event.preventDefault();
	
	//Parse the har file (keep only the things we want)
	//find the <input> field for the file
	var har_file = document.querySelector('#har_file');
	//console.dir(har_file);
	//get the first file of the ones uploaded by the user
	var i = har_file.files[0].text().then(JSON.parse);
	// arg is the name of the JSON attribute that you want to isolate
	//console.log("1");
	console.log(i);
	//console.log("2");
	//Declare the function that handles the promise
	const p_i = (arg) => {
		console.log("A");
		i.then((arg) => {
			//here you can use the parsed value
			var toSend = Object.keys(arg).map((key) => [String(key), arg[key]]);
			//Parser(toSend[0][1]);
			// loop for multiple files?
			var toSend = toSend[0][1];
			console.log("toSend = ");
			console.log(toSend);

			//Extracted Data
			// at the end will contain IPs (any type of IP)
			var entriesServerIPAddress = {};
			// the request methods (POST, GET...)
			var methods = {};
			// the domains
			var domains = {};
			// the ages of the web artifacts
			var ages = {};
			var status = {};
			var content_types = {};
			var expires = {};
			var last_modified = {};
			var max_age = {};
			var cacheability_public = {};
			var cacheability_private = {};
			var cacheability_no_cache = {};
			var cacheability_no_store = {};

			var length = toSend.entries.length;
			//index for the generated objects
			//every object value, at "position" <index>, refers to data for the same artifact
			var index = 0;
			console.log("entries length = " + length);
			
			var hasChanged = false;
			for ( let i = 0; i < length; i++ )
			{	


				//RESPONSE
				//status
				if (toSend.entries[i].response.status !== 'undefined')
				{
					console.log("!!!!" + toSend.entries[i].response.status)
					status[index] = toSend.entries[i].response.status;
					console.log("status = " + status[index]);
				}


				//response headers
				for( let j = 0; j < toSend.entries[i].response.headers.length; j++)
				{
					let newName = toSend.entries[i].response.headers[j].name;
					//console.log("newName = " + newName);
					let newVal = toSend.entries[i].response.headers[j].value;
					//console.log("&&&&");
					//console.log(newVal);
					//console.log(newName);
					//console.log("&&&&");

					//age
					if ( newName === 'age')
					{
						console.log("age = " + newVal);
						ages[index] = newVal;
						hasChanged = true;
						continue;
					}

					//contet-type
					if (newName === 'content-type')
					{
						console.log("contet-type = " + newVal);
						content_types[index] = newVal;
						hasChanged = true;
						continue;
					}

					//expires
					if (newName === 'expires')
					{
						console.log("expires = " + newVal);
						expires[index] = newVal;
						hasChanged = true;
						continue;
					}

					//last-modified
					if (newName === 'last-modified')
					{
						console.log("Last-Modified = " + newVal);
						last_modified[index] = newVal;
						hasChanged = true;
						continue;
					}

					//cache-control
					if (newName === 'cache-control')
					{
						//console.log("Cache-Control = " + newVal);
						//separate the values
						var temp = newVal.split(",");
						//remove whitespace
						temp = temp.filter(function(entry) { return entry.trim() != ''; });
						//console.log(temp);
						for (let k = 0; k < temp.length; k++)
						{
							//max-age
							if (temp[k].includes("max-age") === true)
							{
								let age = temp[k].split("=");
								//age[0] = "max-age"
								//age[1] = <value>
								max_age[index] = age[1];
								console.log("max-age = " + age[1]);
								hasChanged = true;
								continue;
							}

							//public
							if (temp[k].includes("public") === true)
							{
								console.log("public = " + true);
								cacheability_public[index] = true;
								hasChanged = true;
								continue;
							}

							//private
							if (temp[k].includes("private") === true)
							{
								console.log("private = " + true);
								cacheability_private[index] = true;
								hasChanged = true;
								continue;
							}

							//no-cache
							if (temp[k].includes("no-cache") === true)
							{
								console.log("no-cache = " + true);
								cacheability_no_cache[index] = true;
								hasChanged = true;
								continue;
							}

							//no-store
							if (temp[k].includes("no-store") === true)
							{
								console.log("no-store = " + true);
								cacheability_no_store[index] = true;
								hasChanged = true;
								continue;
							}

						}
						continue;
					}


				}


				//REQUEST			
				//entries
				if ( toSend.entries[i].serverIPAddress !== 'undefined' )
				{
					console.log("server ip = " + toSend.entries[i].serverIPAddress);
					entriesServerIPAddress[index] = toSend.entries[i].serverIPAddress;
					hasChanged = true;
				}
				

				if (toSend.entries[i].request.method !== 'undefined')
				{
					console.log("method = " + toSend.entries[i].request.method);
					methods[index] = toSend.entries[i].request.method;
					hasChanged = true;
				}
				
				//headers
				//Host
				if (toSend.entries[i].request.headers[0].value !== 'undefined')
				{
					console.log("domain = " + toSend.entries[i].request.headers[0].value);
					domains[index] = toSend.entries[i].request.headers[0].value;
				}

				//increment index
				if (hasChanged === true)
				{
					index++;
				}

				console.log("*******");
			};
			
			//append extracted data to an object array to send to server
			//data = [];
			//data.push(methods);
			//data.push(entriesServerIPAddress);
			console.log("methods = " + methods);
			let data = {};
			//methods['tis'] = 13;
			//methods['sds'] = 345;
			methods['156'] = 345;
			data.methods = methods;
			data.entriesServerIPAddress = entriesServerIPAddress;
			data.domains = domains;
			data.ages = ages;
			data.status = status;
			data.content_types = content_types;
			data.expires = expires;
			data.last_modified = last_modified;
			data.max_age = max_age;
			data.cacheability_public = cacheability_public;
			data.cacheability_private = cacheability_private;
			data.cacheability_no_cache = cacheability_no_cache;
			data.cacheability_no_store = cacheability_no_store;
			//data.v = 12; //server side test for > 13 arguments sent case
			console.dir(data);
			console.log("status: ");
			console.dir(status);
			console.dir("last_modified = ");
			console.dir(last_modified);
			
			//POST the data to the server
			fun = function () 
					{
						console.log("Data successfully sent to server.");
						//console.log( $( ".result" ).html(data) );
					};
			
			console.log("Hello");
			console.log(entriesServerIPAddress);
			$.ajax({
			  type: "POST",
			  url: "controllers/handle_incoming_har_data.php",
			  data: data,
			  success: fun,
			});			
		});
	};
	//console.log("3");
	// call the promise function
	a = p_i("log");
	
	

});
/*
function(event) {

  event.preventDefault();
  
  console.log("11");
  
  console.log("form = " + this);
  console.dir(this);
  const json = serialize_form(this);
  console.log("JSON? = " + json);
  console.log("12");
  	fun = function ( data ) 
			{
				console.log("in here");
				console.log( $( ".result" ).html(data) );
			};
	dat = { "a" : 1 };
	console.log("Hello");
	$.ajax({
	  type: "POST",
	  url: "upload.php",
	  data: dat,
	  success: fun,
	});
  
  });
//*/
//let data = null;

console.log("1");

/*
function Parser2(event){
	// Prevent the default action for cancelable events
	//console.log(event);
	event.preventDefault();
	fun = function ( data ) 
			{
				console.log("Data successfully sent to server.");
				//console.log( $( ".result" ).html(data) );
			};
	dat = { "a" : 1 };
	console.log("Hello");
	$.ajax({
	  type: "POST",
	  url: "upload.php",
	  data: dat,
	  success: fun,
	});
}
*/
</script> 