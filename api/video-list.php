<?php

require_once 'index.php';

$query	 = $db->prepare('SELECT id, name, code FROM video');
$query->execute();
$videos	 = $query->fetchAll(PDO::FETCH_ASSOC);
// Return List of results
exit(json_encode(array(
	'status'	 => 1,
	'message'	 => 'Success',
	'data'		 => $videos
)));
