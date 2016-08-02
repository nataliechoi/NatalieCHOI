<?php

require_once 'index.php';

$help_id = filter_input(INPUT_GET, 'id');
// Check if GET variables is valid
if (!is_null($help_id) && !empty($help_id)) {
	$query	 = $db->prepare('SELECT name, tel, email, address FROM seekhelp WHERE id = ?');
	$query->execute(array($help_id));
	$info	 = $query->fetch(PDO::FETCH_ASSOC);
	// Successful and return detail
	exit(json_encode(array(
		'status'	 => 1,
		'message'	 => 'Success',
		'data'		 => $info
	)));
}
// Fail if variables is invalid
exit(json_encode(array(
	'status'	 => 0,
	'message'	 => 'Bad Request'
)));
