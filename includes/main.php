<?php

/**
 * Include the libraries
 */
require_once __DIR__."/idiorm.php";//--DB abstract layer
require_once __DIR__."/page_functions.php";//--page helpers
require_once __DIR__."/Role.class.php";//class role

/**
 * Configure DB
 */
$db_host = 'localhost';
$db_name = 'jsolsma1_test';
$db_user = 'jsolsma1_test';
$db_pass = '1qasw23ed';

ORM::configure("mysql:host=$db_host;dbname=$db_name");
ORM::configure("username", $db_user);
ORM::configure("password", $db_pass);

// Set the database connection to UTF-8
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

/**
 * Configure the session
 */

session_name('ctv');

// Uncomment to keep people logged in for a week
//session_set_cookie_params(60 * 60 * 24 * 7);

session_start();

/******************************
 * 		Email settings
 *****************************/

// The "from" email address that is used in the emails that are sent to users.
// Some hosting providers block outgoing email if this address
// is not registered as a real email account on their system, so put a real one here.

$fromEmail = 'hzheng@chalkthevote.com';

if(!$fromEmail){
	// This is only used if you haven't filled an email address in $fromEmail
	$fromEmail = 'noreply@'.$_SERVER['SERVER_NAME'];
}


?>