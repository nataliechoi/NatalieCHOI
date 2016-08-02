<?php

require_once '../index.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : NULL;
// Check if user session existed
if ( !is_null($user) && !empty($user) ) {
	// Successful and return user data
	exit(json_encode(array(
		'status'	 => 1,
		'message'	 => 'User',
		'data'		 => $user
	)));
}
// Fail if not existed
exit(json_encode(array(
	'status'	 => 0,
	'message'	 => 'Guest'
)));
