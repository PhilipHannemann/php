<?php  
	require_once("Website.php");

	$website = new Website();

	$username = $email = $password = "";
/*

	if (isset($_POST["username"])) {
		$username = fix_string($_POST["username"]);
	}
	if (isset($_POST["email"])) {
		$email = fix_string($_POST["email"]);
	}
	if (isset($_POST["password"])) {
		$password = fix_string($_POST["password"]);
	}*/


	validate();

	function validate(){
		$fail ="";// validateUserName($username);
		//$fail += validateEmail($email);
		//$fail += validatePassword($password);

		if($fail == ""){
			echo "everything ok";
		}else{
			echo "$fail";
		}
		
	}

/*
	function validateUserName($name){
		if (preg_match("/[ *]/", $name) || $name == ""){
			return "No Username was entered.\n";
		}elseif (strlen($name) < 8){
			return "Usernames must be at least 8 characters.\n";
		}elseif (preg_match("/[^a-zA-Z0-9_-]/", $name)){
			return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\n";
		}

		return "";
	}



	function validateEmail($email){
		if ($email == ""){
			return "No Email was entered.\n";
		}elseif (!preg_match("/^(([^<>()\[\]\\.,;:\s@']+(\.[^<>()\[\]\\.,;:\s@']+)*)|('.+'))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/", $email)){
			return "The Email address is invalid.\n";
		}
		
		return "";
	}

	function validatePassword($pwd){
			if ($pwd == ""){
				return "No Password was entered.\n";
			}
			elseif (strlen($pwd) < 6){
				return "Passwords must be at least 6 characters.\n";
			}
			elseif (strlen($pwd) > 12) {return ""}
			elseif (!preg_match("/[a-z]/", $pwd) || !preg_match("/[A-Z]/", $pwd) || !preg_match("/[0-9]/", $pwd){
				return "Passwords require one each of a-z, A-Z and 0-9.\n";
			}
			return "";
	}*/


	function fix_string($string){
		if(get_magic_quotes_gpc()){
			$string = stripcslashes($string);
		}

		return htmlentities($string);
	}


?>