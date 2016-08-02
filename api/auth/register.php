<?php

require_once '../index.php';

$first_name	 = filter_input(INPUT_POST, 'first_name');
$last_name	 = filter_input(INPUT_POST, 'last_name');
$email		 = filter_input(INPUT_POST, 'email');
$password	 = filter_input(INPUT_POST, 'password');
$password2	 = filter_input(INPUT_POST, 'password2');

if ( // Check if POST variables is valid
	!is_null($first_name) && !empty($first_name) &&
	!is_null($last_name) && !empty($last_name) &&
	!is_null($email) && !empty($email) &&
	!is_null($password) && !empty($password) &&
	!is_null($password2) && !empty($password2)
) {
	// Check if password enter correctly twice
	if ($password === $password2) {
		$query = $db->prepare('SELECT * FROM user WHERE email = ?');
		$query->execute(array($email));
		// Check if email has already registered
		if($query->rowCount() === 0) {
			$query = $db->prepare('INSERT INTO user (first_name, last_name, email, password, create_time) VALUE (?, ?, ?, ?, ?)');
			$query->execute(array($first_name, $last_name, $email, $password, time()));
			// Successful
			exit(json_encode(array(
				'status'	 => 1,
				'message'	 => 'Congratulation! You can login now', 
				'data'		 => NULL
			)));
		} else {
			// Fail if email already registered
			exit(json_encode(array(
				'status'	 => 0,
				'message'	 => 'Email has been registered'
			)));
		}
	} else {
		// Fail if password is not enter correctly twice
		exit(json_encode(array(
			'status'	 => 0,
			'message'	 => 'Confirm Password is incorrect'
		)));
	}
}
// Fail if variables is invalid
exit(json_encode(array(
	'status'	 => 0,
	'message'	 => 'One or more fields are invalid'
)));
