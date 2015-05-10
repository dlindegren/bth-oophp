<?php

/**
* Class CUser is the administrative part of the system. It contains functionality for admins/users.
*/
class CUser {

	private $db;
	private $out ="";

	/** 
	* @param object $db
	*/
	public function __construct($db) {
		$this->db = $db;
	}

	/** 
	* A function that returns a string for the user.
	* @return $this->out
	*/
	public function getOutput() {
		return $this->out;
	}

	/** 
	* A function that redirects a user depending on whether a user is logged in or not.
	*/
	public function redirectUser() {
		if($this->statusCheck() == true) {
			header("location: usercontroller.php");
		} else {
			header("location: login.php");
		}
	}

	/** 
	* HTML-markup for login
	*/
	public function createLoginForm() {
		$this->out .= "
			<form method='post'>
    			<fieldset>
	        		<legend>Login</legend>
	        		Logga in med:</br> 
	        		<ul>
	        			<li>doe:doe</li>
	        			<li>admin:admin</li>
	        		</ul>
	        		<p>Användare:<br/><input type='text' name='acronym' value=''/></p>
	        		<p>Lösenord:<br/><input type='password' name='password' value=''/></p>
	        		<p><input type='submit' name='login' value='Logga In'/></p>
    			</fieldset>
			</form>
		";
	}

	/** 
	* HTML-markup for logout
	*/
	public function createLogoutForm() {
		$this->out = "
			<form method=post> 
	  			<fieldset> 
	      		<legend>Logga ut ({$_SESSION['user']->name})</legend> 
      				<p><input type='submit' name='logout' value='Logga Ut'/></p> 
  				</fieldset> 
			</form>
		";
	}

	/** 
	* HTML-markup for registering
	*/
	public function createRegisterForm() {
		$this->out .= "
			<form method='post'>
    			<fieldset>
	        		<legend>Registrera</legend>
	        		<p>Användare:<br/><input type='text' name='acronym' value=''/></p>
	        		<p>Namn:<br/><input type='text' name='name' value=''/></p>
	        		<p>Lösenord:<br/><input type='password' name='password' value=''/></p>
	        		<p><input type='submit' name='register' value='Registrera användare'/></p>
    			</fieldset>
			</form>
		";
	}
	
	/** 
	* Register a user.
	*/
	public function register() {	
		if(isset($_POST['register'])) {
			if(!empty($_POST['acronym']) && !empty($_POST['password'])) {

				$sql = "INSERT INTO USER (acronym, name, salt) VALUES (?, ?, unix_timestamp())";
  				$params = array($_POST['acronym'],$_POST['name']);
  				$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);

  				$sql = "UPDATE USER SET password = md5(concat('?', salt)) WHERE acronym = ?";
  				$params = array($_POST['acronym']);
  				$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);

  				header("Location: login.php");

			} else {
				$this->out .= "<p>Ange användare och lösenord.</p>";
			}		
		} 
	}

	/** 
	* Login a user if password and acronym is correct.
	*/
	public function login() {	
		if(isset($_POST['login'])) {
			if(!empty($_POST['acronym']) && !empty($_POST['password'])) {
				$sql = "SELECT acronym, name FROM user WHERE acronym = ? AND password = md5(concat(?, salt))";
  				$params = array($_POST['acronym'], $_POST['password']);
  				$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);

  				if(isset($res[0])) {
    				$_SESSION['user'] = $res[0];
    				$this->out = "";
  					header('Location: usercontroller.php');
  				}

  				$this->out .= "<p>Inkorrekt!</p>";
			} else {
				$this->out .= "<p>Ange användare och lösenord.</p>";
			}		
		} 
	}

	/** 
	* Logout the user (unsetting the $_SESSION['user'])
	*/
	public function logout() {	
		if(isset($_POST['logout'])) {
  			unset($_SESSION['user']);
  			header('Location: logincontroller.php');
		}
	}

	/** 
	* Check if user is authenticated.
	* @return bool $status
	*/
	public function statusCheck() {	
		$acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;
 
		if($acronym) {
  			$status = True;
		} else {
  			$status = False;
		}
		return $status;
	}
}