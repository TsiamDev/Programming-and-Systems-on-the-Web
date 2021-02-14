<!-- αρχείο για την αλλαγή κωδικού -->
<?php require_once 'controllers/control.php'; ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<!--Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" >

	<link rel="stylesheet" href="style2.css">
	<link rel="stylesheet" href="sidebar.css">
	<script src="ll.js"></script>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" ></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>

	<title>Διαχείρηση Λογαριασμού</title>
	
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
				<form action="reset_password.php" method="post">
					<h3 class="text-center">Αλλαγή κωδικού πρόσβασης</h3>

					<?php if(count($errors) > 0): ?>
						<div class="alert alert-danger">
							<?php foreach($errors as $error): ?>
						 		<li><?php echo $error; ?></li>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					

					<div class="form-group">
						<label for="password">Νέος κωδικός πρόσβασης</label>
						<input type="password" name="new_password" class="form-control form-control-lg">
					</div>

					<div class="form-group">
						<label for="password">Επιβαιβέωση κωδικού πρόσβασης</label>
						<input type="password" name="passwordConf" class="form-control form-control-lg">
					</div>

					<div class="form-group">
						<button type="submit" name="reset-password-btn" class="btn btn-primary btn-block .btn-lg ">Αλλαγή Κωδικού</button>
					</div>
					
				</form>
			</div>
		</div>
	</div>
	<div class="dropdown">
  				<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    			Διαχείρηση Λογαριασμού
  				</button>
  				<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
  							<button class="dropdown-item" type="button"><a href="controllers/ui_for_admin_1.php">Στατιστικά</button>
    					    <button class="dropdown-item" type="button"><a href="manage.php">Αλλαγή password</a></button>
    						<button class="dropdown-item" type="button"><a href="manage2.php">Αλλαγή username</a></button>
    						<div class="dropdown-divider"></div>
    						<button class="dropdown-item" type="button"><a href="index.php?logout=1">Αποσύνδεση</a></button>
  				</div>
			</div>	


</body>
</html>