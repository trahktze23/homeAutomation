<?php
	require_once("dbClass.php");

class Login
{
	 private $error_message = '';

	function Login()
	{
		$this->sitename = 'YourWebsiteName.com';
		$this->rand_key = '0iQx5oBk66oVZep';
	}


	function Log_in()
	{
		if(empty($_POST['username']))
		{
			$this->HandleError("UserName is empty!");
			return false;
		}

		if(empty($_POST['password']))
		{
			$this->HandleError("Password is empty!");
			return false;
		}

		$username = trim($_POST['username']);
		$password = trim($_POST['password']);

		if(!isset($_SESSION)){ session_start(); }
	//print_r($_SESSION);
		if(!$this->CheckLoginInDB($username,$password))
		{
			return false;
		}

		$_SESSION[$this->GetLoginSessionVar()] = $username;

		return true;
	}


	function LogOut()
	{
		session_start();
		$sessionvar = $this->GetLoginSessionVar();
		$_SESSION[$sessionvar]=NULL;
		unset($_SESSION[$sessionvar]);
		$this->RedirectToURL('../../index.php');
	}



	function RedirectToURL($url)
	{
		header("Location: $url");
		exit;
	}

	function GetLoginSessionVar()
	{
		$retvar = md5($this->rand_key);
		$retvar = 'usr_'.substr($retvar,0,10);
		return $retvar;
	}

	function CheckLogin()
	{
		if(!isset($_SESSION)){ session_start(); }
		$sessionvar = $this->GetLoginSessionVar();
		if(empty($_SESSION[$sessionvar]))
		{
			return false;
		}
		return true;
	}


	function CheckLoginInDB($username,$password)
	{
		$db = new database();
		$test = $db->select_v2('users', '', "Select * from users where user='".$username."' and pass='".$password."' ;");
		if( count($test) >0 ) {
			$_SESSION['user_name']  = $test[0]['nume'];
			$_SESSION['user_id'] = $test[0]['id'];

			print_r ($_SESSION);
			return true;
		}
		return true;
	}

	function HandleError($err)
	{
		$this->error_message .= $err."\r\n";
		//echo '<script>  alert("'.$err.'"); </script>';
	}



	function SanitizeForSQL($str)
	{
		if( function_exists( "mysql_real_escape_string" ) )
		{
			$ret_str = mysql_real_escape_string( $str );
		}
		else
		{
			$ret_str = addslashes( $str );
		}
		return $ret_str;
	}


}
