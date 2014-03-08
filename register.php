<?php

require_once 'includes/main.php';
require_once "includes/Register.class.php";


/*--------------------------------------------------
	Handle visits with a login token. If it is
	valid, log the person in.
---------------------------------------------------*/


if(isset($_GET['tkn'])){

	// Is this a valid login token?
	$register = Register::findByToken($_GET['tkn']);

	if($register){
		$register->login();
		redirect('protected.php');
	}
	// Invalid token. Redirect back to the login form.
	redirect('index.php');
}

/*--------------------------------------------------
	Don't show the login page to already 
	logged-in users.
---------------------------------------------------*/

$register = new Register(null,1);
if($register->loggedIn()){
	redirect('protected.php');
}

/*--------------------------------------------------
	Handle submitting the login form via AJAX
---------------------------------------------------*/
try{

	if(!empty($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])){

		// Output a JSON header
		header('Content-type: application/json');
		
		$email = trim($_POST['email']);//trim spaces
		$password = trim($_POST['password']);
		
		
		if(strlen($password)==0)	
			throw new Exception('Please enter a password.');

		// Is the email address valid?
		if(!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
			throw new Exception('Please enter a valid email.');

		// This will throw an exception if the person is above 
		// the allowed login attempt limits (see functions.php for more):
		rate_limit($_SERVER['REMOTE_ADDR']);
		// Record this login attempt
		rate_limit_tick($_SERVER['REMOTE_ADDR'], $email);


		// Attempt to register
		$register = Register::Registeration($email,$password);

	    if(!$register)
			throw new Exception("Email already exists.");
		
		$message = '';
		$subject = "Welcome to ChalkTheVote!";
		$message = "Thank you for registering at our site!\n\n";
		$message.= "Click this link to activiate your account:\n";
		$message.= get_page_url()."?tkn=".$register->generateToken()."\n\n";
		$message.= "The link will be expire after 10 minutes.";
		$result = send_email($fromEmail, $email, $subject, $message);
		if(!$result){
			throw new Exception("There was an error sending your email. Please try again.");
		}

		die(json_encode(array(
			'message' => 'Thank you for registering! Please check your email to activate your account.'
		)));
		
	}
}
catch(Exception $e){
	die(json_encode(array(
		'error'=>1,
		'message' => $e->getMessage()
	)));
}
?>

<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8"/>
		<title>Register</title>

		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">

		<!-- The main CSS file -->
		<link href="assets/css/style.css" rel="stylesheet" />

		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>

		<form id="login-register" method="post" action="register.php">

			<h1>Register</h1>

			<input type="text" placeholder="your@email.com" name="email" autofocus />
			<input type="password" placeholder="password" name="password"/>
			<button type="submit">Register</button>

			<span></span>

		</form>
        
		<!-- JavaScript Includes -->
	<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="assets/js/script.js"></script>

	</body>
</html>