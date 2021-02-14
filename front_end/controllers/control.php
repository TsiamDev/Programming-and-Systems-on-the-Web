<?php

session_start();

require 'config/db.php';
#require_once 'email.php';

$errors = array();
$username= "";
$email = "";


//sign in button
if(isset($_POST['signup-btn'])){
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$passwordConf = $_POST['passwordConf'];


	//check
	if (empty($username)){
		$errors['username']= "Απαιτείται ονομα χρήστη.";
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$errors['email']="Η διεύθυνση E-mail δεν είναι διαθέσιμη";
	}
	if (empty($email)){
		$errors['email']= "Απαιτείται E-mail.";
	}
	if (empty($password)){
		$errors['password'] = "Απαιτείται κωδικός.";
	}
	if (strlen($password) < 8) {
    $errors['password'] = "Ο κωδικός πρέπει να αποτελείται από τουλάχιστον 8 χαρακτήρες.";
	}
	if (!preg_match("/\d/", $password)) {
    $errors['password'] = "Ο κωδικός πρέπει να αποτελείται από τουλάχιστον 8 χαρακτήρες.";
	}
	if (!preg_match("/[A-Z]/", $password)) {
    $errors['password'] = "Ο κωδικός πρέπει να αποτελείται από τουλάχιστον 1 κεφαλαίο γράμμα.";
	}
	if (!preg_match("/\W/", $password)) {
    $errors['password'] = "Ο κωδικός πρέπει να αποτελείται από τουλάχιστον 1 ειδικό χαρακτήρα.";
	}
	if (preg_match("/\s/", $password)) {
    $errors['password'] = "Ο κωδικός δεν πρέπει να περιέχει whitespace.";
	}

	if ($password !== $passwordConf){
		$errors['password'] = "O κωδικός με τον κωδικό επαλήθευσης δεν ταιριάζουν.";
	}

	$emailQuery = "SELECT * FROM users WHERE email=? LIMIT 1";
	$stmt = $conn->prepare($emailQuery);
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$result = $stmt->get_result();
	$userCount = $result->num_rows;
	$stmt->close();

	if ($userCount > 0){
		$errors['email'] = "Το E-mail χρησιμοποιείται";
	}

	if (count($errors) === 0){
		$password = password_hash($password, PASSWORD_DEFAULT);
	

		$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('sss',$username, $email, $password);
		
		if ($stmt->execute()){
			//login user
			$user_id = $conn->insert_id;
			$_SESSION['id'] = $user_id;
			$_SESSION['username'] = $username;
			$_SESSION['email'] = $email;
			
			$_SESSION['message'] = "You are now logged in!";
			$_SESSION['alert-class'] = "alert-success";
			header('location: index.php');
			exit();
		} else {
			$errors['db_error']="Database error: failed to register";
		}
	}
}


// login

if(isset($_POST['login-btn'])){
	$username = $_POST['username'];
	$password = $_POST['password'];

	
	if (empty($username)){
		$errors['username']= "Απαιτείται ονομα χρήστη.";
	}
	if (empty($password)){
		$errors['password'] = "Απαιτείται κωδικός.";
	}

	if(count($errors) === 0){
		$sql = "SELECT * FROM users WHERE email=? OR username=? LIMIT 1";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ss', $username, $username);
		$stmt->execute();
		$result = $stmt->get_result();
		$user = $result->fetch_assoc();

		if (password_verify($password, $user['password'])){
			$_SESSION['id'] = $user['id'];
			$_SESSION['username'] = $user['username'];
			$_SESSION['email'] = $user['email'];
			$_SESSION['type'] = $user['type'];
			$_SESSION['message'] = "Επιτυχής σύνδεση";
			$_SESSION['alert-class'] = "alert-success";
			if ( $_SESSION['type'] == 'admin' ){
				header('location: index2.php');
			} else{
				header('location: index.php');
			}
			exit();

		} else{
			$errors['login_fail'] = "Δώσατε λάθος στοιχεία.";
		}
	}	
}

//logout button 

if (isset($_GET['logout'])){
	session_destroy();
	unset($_SESSION['id']);
	unset($_SESSION['username']);
	unset($_SESSION['email']);
	header('location: login.php');
	exit();
}

//forgot password

if(isset($_POST['forgot-password'])){
	$email = $_POST['email'];

	//if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
		//$errors['email']="Η διεύθυνση E-mail δεν είναι διαθέσιμη";
	//}
	if (empty($email)){
		$errors['email']= "Απαιτείται E-mail.";
	}

	if (count($errors) == 0){
		$sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
		$result = mysqli_query($conn, $sql);
		$user = mysqli_fetch_assoc($result);
		$id = $user['id'];
		sendPasswordResetLink($email, $id);
		header('location: password_message.php');
		exit(0);

	}

}

//reset password button
if (isset($_POST['reset-password-btn'])) {
	
	$newPassword = $_POST['new_password'];
	$passwordConf = $_POST['passwordConf'];

	if (empty($newPassword) || empty($passwordConf)) {
		$errors['password'] = "Απαιτείται κωδικός πρόσβασης.";
	}
	if ($newPassword !== $passwordConf){
		$errors['password'] = "O κωδικός πρόσβασης με τον κωδικό επαλήθευσης δεν ταιριάζουν.";
	}

	$password = password_hash($newPassword, PASSWORD_DEFAULT);
	$email = $_SESSION['email'];

	if (count($errors) == 0){
		$sql = "UPDATE users SET password='$password' WHERE email='$email'";
		$result = mysqli_query($conn, $sql);
		if($result) {
			header('location: login.php');
			exit(0);
		}
	}
}


//reset username
if (isset($_POST['reset-username-btn'])) {
	$username = $_POST['username'];

	if (empty($username)) {
		$errors['username'] = "Tο πεδίο είναι κενό.";
	}

	$usernameQuery = "SELECT * FROM users WHERE username=? LIMIT 1";
	$stmt = $conn->prepare($usernameQuery);
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$userCount = $result->num_rows;
	$stmt->close();

	if ($userCount > 0){
		$errors['username'] = "Το username χρησιμοποιείται";
	}
	
	$_SESSION['username'] = $username;
	
	$email = $_SESSION['email'];

	if (count($errors) == 0){
		$sql = "UPDATE users SET username='$username' WHERE email='$email'";
		$result = mysqli_query($conn, $sql);
		if($result) {
			header('location: index.php');
			exit(0);
		}
	}
}


function resetPassword($token)
{
	global $conn;
	$sql = "SELECT * FROM users WHERE token = '$token' LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$user = mysqli_fetch_assoc($result);
	$_SESSION['email'] = $user['email'];
	header('location: reset_password.php');
	exit(0);
}

