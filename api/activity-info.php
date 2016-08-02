<?php

require_once 'index.php';

$activity_id = filter_input(INPUT_GET, 'id');
// Check if GET variables is valid
if ( !is_null($activity_id) && !empty($activity_id) ) {
	$query	 = $db->prepare('SELECT name, content, address, FROM_UNIXTIME(time, "%Y-%m-%d %H:%i:%s") as time FROM activity WHERE id = ?');
	$query->execute(array($activity_id));
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
