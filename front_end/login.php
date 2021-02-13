<?php require_once 'controllers/control.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!--Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" >

	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="titlestyle.css">

	<title>Login</title>
	
</head>
<body>
	<div class="reveal-text">
		<h2>Γρήγορη και εύκολη ανάλυση http αρχείων.</h2>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-4 offset-md-4 form-div login">
				<form action="login.php" method="post">
					<h3 class="text-center">Σύνδεση</h3>

					<?php if(count($errors) > 0): ?>
						<div class="alert alert-danger">
							<?php foreach($errors as $error): ?>
						 		<li><?php echo $error; ?></li>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>


					<div class="form-group">
						<label for="username">Όνομα Χρήστη ή Εmail</label> 
						<input type="text" name="username" class="form-control form-control-lg" >
					<div class="form-group">
						<label for="password">Κωδικός Πρόσβασης</label>
						<input type="password" name="password" class="form-control form-control-lg" >
					</div>
					<div class="form-group">
						<button type="submit" name="login-btn" class="btn btn-primary btn-block .btn-lg ">Σύνδεση</button>
					</div>
					<p class="text-center">Δεν είσαι μέλος; <a href="signup.php"> Εγγραφή</a></p>
					<div style="font-size: 0.8em; text-align: center;"><a href="forgot_password.php">Ξεχάσατε τον κωδικό σας;</a></div>

				</form>
			</div>
		</div>
	</div>
</body>
</html>