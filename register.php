<?php

require_once 'includes/main.php';
require_once "includes/Register.class.php";
require_once "includes/form_functions.php";


/*--------------------------------------------------
	Visits with a login token.
	If it is valid, log the person in.
---------------------------------------------------*/
if(isset($_GET['tkn'])){

	// Is this a valid login token?
	$register = Register::findByToken($_GET['tkn']);

	if($register){
		$register->login();
		redirect('QBoard/');
	}
	// Invalid token. Redirect to login.
	redirect('login.php');
}

/*--------------------------------------------------
	If already logged in, redirect.
---------------------------------------------------*/
if(Register::S_loggedIn()){
	redirect('login.php');
}

/*--------------------------------------------------
	Submitting the login form via AJAX
---------------------------------------------------*/
valid_register_form();
?>

<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8"/>
		<title>Register</title>

		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">

		<!-- The main CSS file -->
		<link href="assets/css/loginRegister.css" rel="stylesheet" />

		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>

		<form id="login-register" method="post" action="register.php">

			<h1>Register</h1>
			<input type="text" placeholder="Username" name="username" maxlength="15" autofocus />
			<input type="text" placeholder="your@email.com" name="email" />
			<input type="password" placeholder="password" name="password"/>
			<button type="submit">Register</button>

			<span></span>

		</form>
        
		<!-- JavaScript Includes -->
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="assets/js/loginRegister.js"></script>

	</body>
</html>