<?php

require_once 'index.php';

$query		 = $db->prepare('SELECT id, name, FROM_UNIXTIME(time, "%d/%m %H:%i") as time FROM activity');
$query->execute();
$activities	 = $query->fetchAll(PDO::FETCH_ASSOC);
// Return List of results
exit(json_encode(array(
	'status'	 => 1,
	'message'	 => 'Success',
	'data'		 => $activities
)));
