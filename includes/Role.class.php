<?php
/*--------------------------------------------------
	Structure:
	Classes: Role, User, Register, Admin
	
	User, Register, Admin all extends Role
---------------------------------------------------*/
class Role{

	// Protected ORM instance
	protected $orm;

	
 
	/*--------------------------------------------------
	 					Constructor
	 ---------------------------------------------------*/
	public function __construct($param = null ,$pin = 1){

		$tableName = Role::getTableName($pin);

		if($param instanceof ORM){
			// An ORM instance was passed
			$this->orm = $param;
		}
		else if(is_string($param)){
			// An email was passed
			$this->orm = ORM::for_table($tableName)
							->where('email', $param)
							->find_one();
		}
		else{
			$id = 0;
			if(is_numeric($param)){
				// A user id was passed as a parameter
				$id = $param;
			}
			else if(isset($_SESSION['user']['loginid'])){

				// No user ID was passed, look into the sesion
				$id = $_SESSION['user']['loginid'];
			}

			$this->orm = ORM::for_table($tableName)
							->where('id', $id)
							->find_one();
		}
	}
 
	/*--------------------------------------------------
	 					static logout
	 ---------------------------------------------------*/
 	public static function S_logout(){
		if(isset($_SESSION['user'])){
			$_SESSION = array();
			unset($_SESSION);	
		}
	}
	
	/*--------------------------------------------------
	 					static check loggedin
	 ---------------------------------------------------*/
	public static function S_loggedin(){
		return isset($_SESSION['user']['loginid']);
	}
 
	/*--------------------------------------------------
	 					instance login
	 ---------------------------------------------------*/
	public function login(){
		
		
		// Mark the user as logged in
		//$_SESSION['loginid'] = $this->orm->id;
		
		$_SESSION['user']	= array(
			'loginid'	=> $this->orm->id,
			'name'		=> $this->orm->username,
			'gravatar'	=> md5(strtolower(trim($this->orm->email)))
		);

		// Update the last_login db field
		$this->orm->set_expr('last_login', 'NOW()');
		$this->orm->save();
	}

	/*--------------------------------------------------
	 					instance logout
	 ---------------------------------------------------*/
	public function logout(){
		$_SESSION = array();
		unset($_SESSION);
	}

	/*--------------------------------------------------
	 					instance check loggedin
	 ---------------------------------------------------*/
	public function loggedIn(){
		return isset($this->orm->id) && $_SESSION['user']['loginid'] == $this->orm->id;
	}



	public static function getTableName($pin){
		if($pin == 0)
			$table = "registers";
		else if($pin == 1)
			$table = "users";
			
		return $table;
	}

	public static function exists($field,$value,$pin){

		// Does the value of the field exist in the database?
		$result = ORM::for_table(Role::getTableName($pin))
					->where($field, $value)
					->count();
		return $result == 1;
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

?>