<?php

require_once '../index.php';

// Check if user has logged-in
if ( !isset($_SESSION['user']) ) {
	// Fail if user not yet logged-in
	exit(json_encode(array(
		'status'	 => 0,
		'message'	 => 'You must login to access this area'
	)));
}
// AJAX is updating personal information
if ( !is_null(filter_input(INPUT_GET, 'update')) ) {
	// Check if request is using PUT method
	if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'PUT') {
		$data = array();
		parse_str(file_get_contents("php://input"), $data);
		if( // Check if PUT variables is valid
			!is_null($data) && !empty($data) && 
			isset($data['last_name']) && isset($data['first_name'])
		) {
			$query = $db->prepare('UPDATE user SET last_name = ?, first_name = ? WHERE id = ?');
			$query->execute(array($data['last_name'], $data['first_name'], $_SESSION['user']['id']));
			$_SESSION['user']['last_name'] = $data['last_name'];
			$_SESSION['user']['first_name'] = $data['first_name'];
			// Successful
			exit(json_encode(array(
				'status'	 => 1,
				'message'	 => 'Success',
				'data'		 => NULL
			)));
		}
	}
} 
// AJAX is changing password
else if ( !is_null(filter_input(INPUT_GET, 'changepw')) ) {
	// Check if request is using PUT method
	if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'PUT') {
		$data = array();
		parse_str(file_get_contents("php://input"), $data);
		if( // Check if PUT variables is valid
			!is_null($data) && !empty($data) && 
			isset($data['old_password']) && isset($data['new_password']) && isset($data['confirm_password'])
		) {
			// Check if password enter correctly twice
			if ($data['new_password'] === $data['confirm_password']) {
				$query = $db->prepare('SELECT * FROM user where id = ? AND password = ?');
				$query->execute(array($_SESSION['user']['id'], $data['old_password']));
				// Check if old password is matched
				if ( $query->rowCount() === 1 ) {
					$query = $db->prepare('UPDATE user SET password = ? WHERE id = ?');
					$query->execute(array($data['new_password'], $_SESSION['user']['id']));
					// Delete user session to require user re-login
					unset($_SESSION['user']);
					// Successful
					exit(json_encode(array(
						'status'	 => 1,
						'message'	 => 'Success',
						'data'		 => NULL
					)));
				} else {
					// Fail if old password is not match
					exit(json_encode(array(
						'status'	 => 0,
						'message'	 => 'Current Password is incorrect'
					)));
				}
			} else {
				// Fail if password not enter correctly twice
				exit(json_encode(array(
					'status'	 => 0,
					'message'	 => 'Confirm Password is incorrect'
				)));
			}
		}
	}
} 
// AJAX is showing user personal information
else {
	$query = $db->prepare('SELECT first_name, last_name, email, oauth FROM user WHERE id = ?');
	$query->execute(array($_SESSION['user']['id']));
	// Successful and return user personal information
	exit(json_encode(array(
		'status'	 => 1,
		'message'	 => 'Success',
		'data'		 => $query->fetch(PDO::FETCH_ASSOC)
	)));
}
// Fail if anything error
exit(json_encode(array(
	'status'	 => 0,
	'message'	 => 'Bad Request'
)));
