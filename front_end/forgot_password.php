<?php require_once 'controllers/control.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!--Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" >

	<link rel="stylesheet" href="mystyle.css">
	<link rel="stylesheet" href="titlestyle.css">

	<title>Forgot Password</title>
	
</head>
	<body>

		<div class="container">
			<div class="row">
				<div class="col-md-4 offset-md-4 form-div login">
					<form action="forgot_password.php" method="post">
						<h3 class="text-center">Ανάκτηση κωδικού πρόσβασης</h3>
							<p>
							Παρακαλώ εισάγεται το e-mail που χρησιμοποιήσατε κατά την εγγραφή στη σελίδα μας
							και θα σας βοηθήσουμε να ανακτήσετε τον κωδικό σας.
							</p>

						<?php if(count($errors) > 0): ?>
						<div class="alert alert-danger">
							<?php foreach($errors as $error): ?>
						 		<li><?php echo $error; ?></li>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>


						<div class="form-group">
							<input type="email" name="email" class="form-control form-control-lg">
						</div>

						<div class="form-group">
							<button type="submit" name="forgot-password" class="btn btn-primary btn-block .btn-lg ">Ανάκτηση κωδικού</button>
						</div>
					
					</form>
				</div>
			</div>
		</div>
	</body>
</html>