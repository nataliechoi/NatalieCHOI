<?php

require_once '../index.php';

// Delete user session
unset($_SESSION['user']);

exit(json_encode(array(
	'status'	 => 1,
	'message'	 => 'Success', 
	'data'		 => NULL
)));
