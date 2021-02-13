<?php require_once 'controllers/control.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!--Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" >

	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="titlestyle.css">

	<title>Επαναφορά κωδικού πρόσβασης</title>
	
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-4 offset-md-4 form-div login">
				<form action="reset_password.php" method="post">
					<h3 class="text-center">Επαναφορά Κωδικού</h3>

					<?php if(count($errors) > 0): ?>
						<div class="alert alert-danger">
							<?php foreach($errors as $error): ?>
						 		<li><?php echo $error; ?></li>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>


					<div class="form-group">
						<label for="password">Κωδικός Πρόσβασης</label>
						<input type="password" name="password" class="form-control form-control-lg">
					</div>

					<div class="form-group">
						<label for="password">Επιβαιβέωση κωδικού πρόσβασης</label>
						<input type="password" name="passwordConf" class="form-control form-control-lg">
					</div>

					<div class="form-group">
						<button type="submit" name="reset-password-btn" class="btn btn-primary btn-block .btn-lg ">Επαναφορά κωδικού</button>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</body>
</html>