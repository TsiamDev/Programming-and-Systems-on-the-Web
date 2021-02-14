<?php 

require_once 'vendor/autoload.php';
require_once 'config/constants.php';

// Create the Transport
$transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
  ->setUsername(EMAIL)
  ->setPassword(PASSWORD);

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);


function sendPasswordResetLink($userEmail, $token)
{

	global $mailer;

	 $body = '<!DOCTYPE html> 
	<html>
    <head>
    	<meta charset = "UTF-8">
    	<title>Verify email</title>
    </head>
    <body>
    	<div class="wrapper">
    		<p>
    		Γεια σας,

    		Παρακαλώ πατήστε πάνω στο σύνδεσμο για να ανακτήσετε τον κωδικό σας.
    		</p>
    		<a href="http://localhost/yt/index.php?password-token='.$token.'">
    		Επαναφορά κωδικού πρόσβασης
    		</a>
    	</div> 
    </body>
	</html>';


	//δημιουργία μηνύματος
	$message = (new Swift_Message('Επαναφορά κωδικού πρόσβασης'))
		->setFrom(EMAIL)
		->setTo($userEmail)
		->setBody($body, 'text/html');

	$result = $mailer->send($message);

}