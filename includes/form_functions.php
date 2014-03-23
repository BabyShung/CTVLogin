<?php



/*******************************
 * 		functions
 *****************************/
function valid_register_form(){
	try{
	
		if(!empty($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
	
			// Output a JSON header
			header('Content-type: application/json');
			
			//trim spaces
			$email = strtolower(trim($_POST['email']));
			$password = trim($_POST['password']);
			//another username field
			$username = trim($_POST['username']);
	
			if(strlen($username)==0)	
				throw new Exception('Please enter a username.');
			if(strlen($username)>15)	
				throw new Exception('username should be less than 15 characters.');
	
			// Is the email address valid?
			if(!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
				throw new Exception('Please enter a valid email.');
	
			if(strlen($password)==0)	
				throw new Exception('Please enter a password.');
				
			// This will throw an exception if the person is above 
			// the allowed login attempt limits (see functions.php for more):
			rate_limit($_SERVER['REMOTE_ADDR']);
			// Record this login attempt
			rate_limit_tick($_SERVER['REMOTE_ADDR'], $email);
				
			// Attempt to register
			$register = Register::Registeration($email,$username,$password);
	
			if($register == -1)
				throw new Exception("Username already exists.");
			elseif($register == -2)
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

}


function valid_login_form(){
	try{
	
		if(!empty($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
	
			// Output a JSON header
			header('Content-type: application/json');
			
			//trim spaces
			$email = trim($_POST['email']);
			$password = trim($_POST['password']);
	
			if(strlen($email)==0)	//why isset($email) not working???
				throw new Exception('Please enter a username or email.');
	
			if(strlen($password)==0)	
				throw new Exception('Please enter a password.');
	
	
			// This will throw an exception if the person is above 
			// the allowed login attempt limits (see functions.php for more):
			rate_limit($_SERVER['REMOTE_ADDR']);
			// Record this login attempt
			rate_limit_tick($_SERVER['REMOTE_ADDR'], $email);
	
			
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))	//assume using username to login
				$field = 'username';
			else	//assume using email
				$field = 'email';
	
			$user = User::loginCheck($field , $email,$password);
	
			if($user){//query result is back
				$user->login();
				//when javascript off?
				//redirect('protected.php');
				die(json_encode(array(
					'success'=>1
				)));
			}
			elseif(User::exists($field,$email,1))
				throw new Exception("Password incorrect.");
			else
				throw new Exception(ucfirst($field)." not exist.");	
			
			
		}
	}
	catch(Exception $e){
		die(json_encode(array(
			'error'=>1,
			'message' => $e->getMessage()
		)));
	}

}

function send_email($from, $to, $subject, $message){

	// Helper function for sending email
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
	$headers .= 'From: '.$from . "\r\n";

	return mail($to, $subject, $message, $headers);
}

function rate_limit($ip, $limit_hour = 20, $limit_10_min = 10){
	
	// The number of login attempts for the last hour by this IP address

	$count_hour = ORM::for_table('reg_login_attempt')
					->where('ip', sprintf("%u", ip2long($ip)))
					->where_raw("ts > SUBTIME(NOW(),'1:00')")
					->count();

	// The number of login attempts for the last 10 minutes by this IP address

	$count_10_min =  ORM::for_table('reg_login_attempt')
					->where('ip', sprintf("%u", ip2long($ip)))
					->where_raw("ts > SUBTIME(NOW(),'0:10')")
					->count();

	if($count_hour > $limit_hour || $count_10_min > $limit_10_min){
		throw new Exception('Too many login attempts!');
	}
}

function rate_limit_tick($ip, $email){

	// Create a new record in the login attempt table

	$login_attempt = ORM::for_table('reg_login_attempt')->create();

	$login_attempt->email = $email;
	$login_attempt->ip = sprintf("%u", ip2long($ip));

	$login_attempt->save();
}

?>