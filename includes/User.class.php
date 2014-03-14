<?php

class User extends Role{
	

	public static function loginCheck($field, $email,$password){
		// Does the user and pwd exist in the database?
		$result = ORM::for_table('users')
					->where($field, $email)
					->where('pwd', md5($password))
					->find_one();
		if(!$result)
			return false;
		else 
			return new User($result,1);
	}
	

}

?>