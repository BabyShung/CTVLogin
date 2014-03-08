<?php

require_once 'includes/main.php';

/*--------------------------------------------------
	Handle logging out of the system. The logout
	link in protected.php leads here.
---------------------------------------------------*/


if(isset($_GET['logout'])){

	$user = new User();
	if($user->loggedIn())
		$user->logout();
	redirect('index.php');
}


/*--------------------------------------------------
	Don't show the login page to already 
	logged-in users.
---------------------------------------------------*/
$user = new User();
if($user->loggedIn())
	redirect('protected.php');

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


		// Attempt to login
		$user = User::loginCheck($email,$password);

	    if($user){//query result is back
			$user->login();
			//when javascript off?
			//redirect('protected.php');
			die(json_encode(array(
				'success'=>1
			)));
		}
		elseif(User::exists($email))
			throw new Exception("Password incorrect.");
		else
			throw new Exception("Email not exist.");
		
	}
}
catch(Exception $e){

	die(json_encode(array(
		'error'=>1,
		'message' => $e->getMessage()
	)));
}

/*--------------------------------------------------
	Output the login form
---------------------------------------------------*/

?>

<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8"/>
		<title>Login</title>

		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">

		<!-- The main CSS file -->
		<link href="assets/css/style.css" rel="stylesheet" />

		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>

		<form id="login-register" method="post" action="index.php">

			<h1>Login</h1>

			<input type="text" placeholder="your@email.com" name="email" autofocus />
			<input type="password" placeholder="password" name="password" />
			<button type="submit">Login</button>

			<span></span>

		</form>
        
		<!-- JavaScript Includes -->
		<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="assets/js/script.js"></script>

	</body>
</html>