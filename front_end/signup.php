<?php require_once 'controllers/control.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!--Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" >

	<link rel="stylesheet" href="style.css">

	<title>Register</title>
	
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-4 offset-md-4 form-div">
				<form action="signup.php" method="post">
					<h3 class="text-center">Εγγραφή</h3>

					<?php if(count($errors) > 0): ?>
						<div class="alert alert-danger">
							<?php foreach($errors as $error): ?>
						 		<li><?php echo $error; ?></li>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>


					<div class="form-group">
						<label for="username">Όνομα Χρήστη</label>
						<input type="text" name="username" value="<?php echo $username; ?>" class="form-control form-control-lg">
					</div>
						<div class="form-group">
						<label for="email">Email</label>
						<input type="email" name="email" value="<?php echo $email; ?>" class="form-control form-control-lg">
					</div>
					<div class="form-group">
						<label for="password">Κωδικός Πρόσβασης</label>
						<input type="password" name="password" class="form-control form-control-lg">
						<small id="passwordHelp" class="form-text text-muted">Ο κωδικός πρέπει
							να είναι τουλάχιστον 8 χαρακτήρες και να περιέχει τουλάχιστον ένα κεφαλαίο γράμμα, ένα αριθμό
							και κάποιο σύμβολο (π.χ. #$*&@).</small>
					</div>
					<div class="form-group">
						<label for="passwordConf">Επιβεβαίωση Κωδικού Πρόσβασης</label>
						<input type="password" name="passwordConf" class="form-control form-control-lg">
					</div>
					<div class="form-group">
						<button type="submit" name="signup-btn" class="btn btn-primary btn-block .btn-lg ">Εγγραφή</button>
					</div>
					<br><p class="text-center">Είσαι ήδη μέλος; <a href="login.php"> Σύνδεση</a></p></br>


				</form>
			</div>
		</div>
	</div>
</body>
</html>