<?php

require_once '../index.php';

$auth	 = filter_input(INPUT_POST, 'auth');
$data	 = filter_input(INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if ( // Check if POST variables is valid
	!is_null($auth) && !empty($auth) &&
	!is_null($data) && !empty($data)
 ) {
	$query	 = $db->prepare('SELECT * FROM user WHERE token = ?');
	$query->execute(array($data['id']));
	// Check if OAuth user has already registered
	if ( ($user	 = $query->fetch(PDO::FETCH_ASSOC) ) ) {
		unset($user['password'], $user['create_time']);
		// Save user to user session
		$_SESSION['user'] = $user;
	} else {
		// Register OAuth user if user not yet registered
		$query				 = $db->prepare('INSERT INTO user (first_name, last_name, oauth, token, create_time) VALUE (?, ?, ?, ?, ?)');
		$query->execute(array($data['first_name'], $data['last_name'], $auth, $data['id'], time()));
		// Save user to user session
		$_SESSION['user']	 = array(
			'id'		 => $db->lastInsertId(),
			'first_name' => $data['first_name'],
			'last_name'	 => $data['last_name'],
			'email'		 => NULL,
			'oauth'		 => $auth,
			'token'		 => $data['id']
		);
	}
	$_SESSION['user']['avatar'] = $auth === 'google' ? str_replace('?sz=50', '?sz=140', $data['picture']) : "{$data['picture']}?width=140";
	// Successful
	exit(json_encode(array(
		'status'	 => 1,
		'message'	 => 'Success',
		'data'		 => NULL
	)));
}
// Fail if variables is invalid
exit(json_encode(array(
	'status'	 => 0,
	'message'	 => 'No Auth'
)));
