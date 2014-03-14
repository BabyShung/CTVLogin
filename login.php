<?php

require_once 'includes/main.php';
require_once "includes/User.class.php";
require_once "includes/form_functions.php";


/*--------------------------------------------------
	Handle logging out of the site.
---------------------------------------------------*/
if(isset($_GET['logout'])){

	User::S_logout();
	redirect('login.php');
}

/*--------------------------------------------------
	If already logged in, redirect.
---------------------------------------------------*/
if(User::S_loggedIn())
	redirect('QBoard/');
	
/*--------------------------------------------------
	Submitting the login form via AJAX
---------------------------------------------------*/
valid_login_form();


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

		<form id="login-register" method="post" action="login.php">

			<h1>Login</h1>

			<input type="text" placeholder="username or email" name="email" autofocus />
			<input type="password" placeholder="password" name="password" />
			<button type="submit">Login</button>

			<span></span>

		</form>
        
		<!-- JavaScript Includes -->
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="assets/js/script.js"></script>

	</body>
</html>