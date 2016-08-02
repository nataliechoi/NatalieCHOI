<?php

require_once 'index.php';

$query	 = $db->prepare('SELECT id, name, target FROM tnr');
$query->execute();
$tnrs	 = $query->fetchAll(PDO::FETCH_ASSOC);
// Return List of results
exit(json_encode(array(
	'status'	 => 1,
	'message'	 => 'Success',
	'data'		 => $tnrs
)));
