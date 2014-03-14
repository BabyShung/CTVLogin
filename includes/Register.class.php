<?php

class Register extends Role{

	 /*--------------------------------------------------
	 Activate account:
	  Find a user by a token string. Only valid tokens are taken into
	  consideration. A token is valid for 10 minutes after it has been generated.
	  @param string $token The token to search for
	  @return User
	 ---------------------------------------------------*/
	public static function findByToken($token){
		
		// find it in the database and make sure the timestamp is correct
		$result = ORM::for_table('registers')
						->where('token', $token)
						->where_raw('token_validity > NOW()')
						->find_one();
		
		if(!$result)
			return false;
		//update activate in registers table
		$result->activate = 1;
		$result->save();
		
		$resultUser = ORM::for_table('users')
						->where('email', $result->email)
						->find_one();
		//update activate in users table				
		$resultUser->activate = 1;
		$resultUser->save();

		return new Register($resultUser,1);
	}

	/*----------------------------
		perform registration
	----------------------------*/
	public static function Registeration($email,$username,$passward){

		// If such a register already exists, return false
		if(Register::exists('username',$username,0)){
			return -1;
		}
		
		if(Register::exists('email',$email,0)){
			return -2;
		}
		
		// Otherwise, create it and return it
		return Register::create($email,$username,$passward);
	}

	private static function create($email,$username,$passward){
		// insert two tables,first users,second registers
		//registers id can be users' foreign key
		
		$resultUser = ORM::for_table('users')->create();
		$resultUser->email = $email;
		$resultUser->username = $username;
		$resultUser->pwd = md5($passward);
		$resultUser->save();
		
		$result = ORM::for_table('registers')->create();
		$result->email = $email;
		$result->username = $username;
		$result->save();

		return new Register($result,0);
	}

  	/*----------------------------------------------------------------------------------
		Generates a new SHA1 login token, writes it to the database and returns it.
	----------------------------------------------------------------------------------*/
	public function generateToken(){
		// generate a token for the logged in user. Save it to the database.

		$token = sha1($this->email.time().rand(0, 1000000));

		// Save the token to the database, 
		// and mark it as valid for the next 10 minutes only

		$this->orm->set('token', $token);
		$this->orm->set_expr('token_validity', "ADDTIME(NOW(),'0:10')");
		$this->orm->save();

		return $token;
	}
 
}

?>