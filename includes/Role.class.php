<?php

class Role{

	// Private ORM instance
	private $orm;


	/**
	 * Create a new user and save it to the database
	 * @param string $email The user's email address
	 * @return User
	 */

	private static function create($email){

		// Write a new user to the database and return it

		$result = ORM::for_table('reg_users')->create();
		$result->email = $email;
		$result->save();

		return new User($result);
	}

	/**
	 * Check whether such a user exists in the database and return a boolean.
	 * @param string $email The user's email address
	 * @return boolean
	 */

	public static function exists($email){

		// Does the user exist in the database?
		$result = ORM::for_table('reg_users')
					->where('email', $email)
					->count();

		return $result == 1;
	}

	/**
	 * Create a new user object
	 * @param $param ORM instance, id, email or null
	 * @return User
	 */

	public function __construct($param = null){

		if($param instanceof ORM){

			// An ORM instance was passed
			$this->orm = $param;
		}
		else if(is_string($param)){

			// An email was passed
			$this->orm = ORM::for_table('reg_users')
							->where('email', $param)
							->find_one();
		}
		else{

			$id = 0;

			if(is_numeric($param)){
				// A user id was passed as a parameter
				$id = $param;
			}
			else if(isset($_SESSION['loginid'])){

				// No user ID was passed, look into the sesion
				$id = $_SESSION['loginid'];
			}

			$this->orm = ORM::for_table('reg_users')
							->where('id', $id)
							->find_one();
		}

	}


	/**
	 * Login this user
	 * @return void
	 */

	public function login(){
		
		// Mark the user as logged in
		$_SESSION['loginid'] = $this->orm->id;

		// Update the last_login db field
		$this->orm->set_expr('last_login', 'NOW()');
		$this->orm->save();
	}

	/**
	 * Destroy the session and logout the user.
	 * @return void
	 */

	public function logout(){
		$_SESSION = array();
		unset($_SESSION);
	}

	/**
	 * Check whether the user is logged in.
	 * @return boolean
	 */

	public function loggedIn(){
		return isset($this->orm->id) && $_SESSION['loginid'] == $this->orm->id;
	}

	/**
	 * Check whether the user is an administrator
	 * @return boolean
	 */

	public function isAdmin(){
		return $this->rank() == 'administrator';
	}

	/**
	 * Find the type of user. It can be either admin or regular.
	 * @return string
	 */

	public function rank(){
		if($this->orm->rank == 1){
			return 'administrator';
		}

		return 'regular';
	}

	/**
	 * Magic method for accessing the elements of the private
	 * $orm instance as properties of the user object
	 * @param string $key The accessed property's name 
	 * @return mixed
	 */

	public function __get($key){
		if(isset($this->orm->$key)){
			return $this->orm->$key;
		}

		return null;
	}
}