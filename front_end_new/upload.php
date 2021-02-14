<?php include 'filesLogic.php' ?>

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

	<!-- fs 
	<script src="..\front_end\dependencies\fs.js-master\fs.js"/> -->

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
					<button id="upload_btn" type="submit" class="btn btn-primary btn-lg btn-block" >upload</button>
					<button id="save_btn" type="submit" class="btn btn-primary btn-lg btn-block" >save</button/>
				</div>

				<div class="up">
					<h5>Το αρχέιο πρέπει να έχει επέκταση .har . Μέγιστο μέγεθος αρχείου 10MB.</h5>
				</div>
				
				<div id="myDIV">
					<input type="file" name="myfile" id="har_file"/> 	
					<p id="notify_user" value=""></p>
					<button id="submit_btn" type="submit" class="btn btn-primary btn-lg btn-block" name="submit">submit</button>
				</div>
			</div>
		</div>
	</div>

	<div class="dropdown">
  				<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    			Διαχείρηση Λογαριασμού
  				</button>
  				<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
  							<button class="dropdown-item" type="button"><a href="controllers/check_user_clearence_level.php">Στατιστικά</button>
    					    <button class="dropdown-item" type="button"><a href="manage.php">Αλλαγή password</button>
    						<button class="dropdown-item" type="button"><a href="manage2.php">Αλλαγή username</button>
    						<div class="dropdown-divider"></div>
    						<button class="dropdown-item" type="button"><a href="index.php?logout=1">Αποσύνδεση</button>
  				</div>
			</div>	

</body>
</html>

<script type="text/JavaScript" defer> 

console.log('111');

//'cache' html elements
//TODO: check if they have already been initialized
var save_btn = document.querySelector('#save_btn');
var upload_btn = document.querySelector('#upload_btn'); 
var submit_btn = document.querySelector('#submit_btn')

var har_file = document.querySelector('#har_file');
var myDIV = document.querySelector('#myDIV');

var notify_user = document.querySelector('#notify_user');


//maybe not the best way to do this, but it will do for now
$(save_btn).hide();
$(upload_btn).hide();
$(notify_user).hide();

//based on: https://stackoverflow.com/questions/10211145/getting-current-date-and-time-in-javascript
//the idea is to save the file locally to a user specified directory
$(save_btn).click( function(){
	
	//Generate current timestamp (to use as file name)
	var currentdate = new Date(); 
	var fileName = "[" + currentdate.getDate() + "-"
                + (currentdate.getMonth() + 1)  + "-" 
                + currentdate.getFullYear() + " "  
                + currentdate.getHours() + "-"  
                + currentdate.getMinutes() + "-" 
                + currentdate.getSeconds() + "]";
	console.log(fileName);
	
	//no call by reference in js :/
	store_locally(file);
	//get_local_files_demo();	
});

function get_local_files_demo(db)
{
	console.log("get_local_files");
	var transaction = db.transaction(["files"]);
	var objectStore = transaction.objectStore("files");
	var request = objectStore.get("444-44-4444");
	request.onerror = function(event) {
	  // Handle errors!
	  console.log(request.errorCode);
	};
	request.onsuccess = function(event) {
	  // Do something with the request.result!
	  console.log("Name for SSN 444-44-4444 is " + request.result.name);
	};
}

// based on: https://developer.mozilla.org/en-US/docs/Web/API/IndexedDB_API/Using_IndexedDB
// <file> is the parsed json data
function store_locally(file)
{
	console.log("store_locally");
	
	var db;
	
	const customerData = [
	  { ssn: "444-44-4444", name: "Bill", age: 35, email: "bill@company.com" },
	  { ssn: "555-55-5555", name: "Donna", age: 32, email: "donna@home.org" }
	];
	
	
	// I took the approach to ignore browsers that don't support IndexedDB for simplicity
	// also! I dont care about Internet Explorer.. Seriously, who uses that thing @ 2021?
	if (!window.indexedDB) {
		console.log("fail local");
		die("browser does not support IndexedDB - can't store data locally");
	}

	// open db
	var request = indexedDB.open("local_storage_db");

	request.onerror = function(event) {
	  // Handle errors.
	  console.log(event.errorCode);
	};
	
	// could also use <onsuccess> instead (?) which is more widespread
	// as onupgradeneeded is only implemented in <recent browsers>
	// says developer.mozilla.org
	request.onupgradeneeded = function(event) {
		console.log("upgrade");
	  db = event.target.result;

	  // Create an objectStore to hold information about our customers. We're
	  // going to use "ssn" as our key path because it's guaranteed to be
	  // unique - or at least that's what I was told during the kickoff meeting.
	  var objectStore = db.createObjectStore("files", { keyPath: "ssn" });

	  //I could also create indexes for searching the db, but 
	  //this functionality is not specified in the exercise
	  //so I chose to omit this
	
	  // Create an index to search customers by name. We may have duplicates
	  // so we can't use a unique index.
	  objectStore.createIndex("name", "name", { unique: false });

	  // Create an index to search customers by email. We want to ensure that
	  // no two customers have the same email, so use a unique index.
	  objectStore.createIndex("email", "email", { unique: true });

	  // Use transaction oncomplete to make sure the objectStore creation is
	  // finished before adding data into it.
	  objectStore.transaction.oncomplete = function(event) {
		// Store values in the newly created objectStore.
		var customerObjectStore = db.transaction("files", "readwrite").objectStore("files");
		customerData.forEach(function(customer) {
		  customerObjectStore.add(customer);
		});
		console.log("Successfully stored in local db!");
		get_local_files_demo(db);
	  };
	  
	  
	};
}

$(submit_btn).click(function(){
	//console.log("a");
	//console.log(notify_user);
	
	//check if a file was selected
	if(har_file.files[0] != null)
	{
		//console.log("b");
		$(save_btn).show();
		$(upload_btn).show();
		$(notify_user).hide();
		$(submit_btn).hide();
	}else{
		$(notify_user).html("Choose a har file first!");
		$(notify_user).show();
	}
});

//"clears" the user's selected file 
//BY OBLITERATING THE INPUT ELEMENT MUHAHAHAHHA
function clear_har_input()
{
	//clear old element
	har_file.remove();
	
	//create new 'identical' element
	har_file = document.createElement('input');
	har_file.type = 'file';
	har_file.name = 'myfile';
	har_file.id = 'har_file';

	//add to the html
	myDIV.prepend(har_file);
}

//$('#submit_form').on('submit', function (event) {
$(upload_btn).click(function (event) {	
	console.log('222');
	// Prevent the default action for the forms' (cancelable) event: submit
	//console.log(event);
	event.preventDefault();
	
	//Parse the har file (keep only the things we want)
	//find the <input> field for the file
	//var har_file = document.querySelector('#har_file');
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
			var req_content_types = {};
			var req_max_stales = {};
			var req_min_freshs = {};
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
					//console.log("!!!!" + toSend.entries[i].response.status)
					status[index] = toSend.entries[i].response.status;
					//console.log("status = " + status[index]);
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
						//console.log("age = " + newVal);
						ages[index] = newVal;
						hasChanged = true;
						continue;
					}

					//contet-type
					if (newName === 'content-type')
					{
						//console.log("content-type = " + newVal);
						content_types[index] = newVal;
						hasChanged = true;
						continue;
					}

					//expires
					if (newName === 'expires')
					{
						//console.log("expires = " + newVal);
						expires[index] = newVal;
						hasChanged = true;
						continue;
					}

					//last-modified
					if (newName === 'last-modified')
					{
						//console.log("Last-Modified = " + newVal);
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
								//console.log("max-age = " + age[1]);
								hasChanged = true;
								continue;
							}

							//public
							if (temp[k].includes("public") === true)
							{
								//console.log("public = " + true);
								cacheability_public[index] = true;
								hasChanged = true;
								continue;
							}

							//private
							if (temp[k].includes("private") === true)
							{
								//console.log("private = " + true);
								cacheability_private[index] = true;
								hasChanged = true;
								continue;
							}

							//no-cache
							if (temp[k].includes("no-cache") === true)
							{
								//console.log("no-cache = " + true);
								cacheability_no_cache[index] = true;
								hasChanged = true;
								continue;
							}

							//no-store
							if (temp[k].includes("no-store") === true)
							{
								//console.log("no-store = " + true);
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
					//console.log("server ip = " + toSend.entries[i].serverIPAddress);
					entriesServerIPAddress[index] = toSend.entries[i].serverIPAddress;
					hasChanged = true;
				}
				

				if (toSend.entries[i].request.method !== 'undefined')
				{
					//console.log("method = " + toSend.entries[i].request.method);
					methods[index] = toSend.entries[i].request.method;
					hasChanged = true;
				}
				
				//headers
				//Host
				if (toSend.entries[i].request.headers[0].value !== 'undefined')
				{
					//console.log("domain = " + toSend.entries[i].request.headers[0].value);
					domains[index] = toSend.entries[i].request.headers[0].value;
				}

				
				for( let j = 0; j < toSend.entries[i].request.headers.length; j++)
				{
					let newName = toSend.entries[i].request.headers[j].name;
					//console.log("newName = " + newName);
					let newVal = toSend.entries[i].request.headers[j].value;
						
					//content-type
					if ((newName === 'Content-Type') || (newName === 'content-type'))
					{
						//console.log("content-type = " + newVal);
						req_content_types[index] = newVal;
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
							//max-stale
							if (temp[k].includes("max-stale") === true)
							{
								let stale = temp[k].split("=");
								//stale[0] = "max-stale"
								//stale[1] = <value>
								req_max_stales[index] = stale[1];
								//console.log("max-stale = " + stale[1]);
								hasChanged = true;
								continue;
							}
							
							//min-fresh
							if (temp[k].includes("min-fresh") === true)
							{
								let fresh = temp[k].split("=");
								//fresh[0] = "min-fresh"
								//fresh[1] = <value>
								req_min_freshs[index] = fresh[1];
								//console.log("min-fresh = " + fresh[1]);
								hasChanged = true;
								continue;
							}
						}
						continue;
					}
					
				}
	


				//increment index
				if (hasChanged === true)
				{
					index++;
				}

				//console.log("*******");
			};
			
			//append extracted data to an object array to send to server
			//data = [];
			//data.push(methods);
			//data.push(entriesServerIPAddress);
			//console.log("methods = " + methods);
			let data = {};
			//methods['tis'] = 13;
			//methods['sds'] = 345;
			//methods['156'] = 345;
			data.req_max_stales = req_max_stales;
			data.req_min_freshs = req_min_freshs;
			data.req_content_types = req_content_types;
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
			console.log("data: \r\n"); 
			console.dir(data);
			//console.log("status: ");
			//console.dir(status);
			//console.dir("last_modified = ");
			//console.dir(last_modified);
			
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
	
	//UI/html stuff
	$(save_btn).hide();
	$(upload_btn).hide();
	$(submit_btn).show();
	
	//clear the selected file
	clear_har_input();

});
console.log("1");
</script> 
