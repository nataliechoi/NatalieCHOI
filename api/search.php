<?php

require_once 'index.php';

$keyword = filter_input(INPUT_POST, 'keyword');
// Check if POST variables is valid
if ( !is_null($keyword) && !empty($keyword) ) {
	// Search areas
	$searchs = array('activity', 'drug', 'seekhelp', 'tnr', 'video');

	$result = array();
	foreach ( $searchs as $search ) {
		$query = $db->prepare("SELECT id, name FROM {$search} WHERE name LIKE ? OR content LIKE ?");
		$query->execute(array("%{$keyword}%", "%{$keyword}%"));
		// Add search result to list
		foreach ( $query->fetchAll(PDO::FETCH_ASSOC) as $row ) {
			array_push($result, array(
				'type'	 => $search,
				'name'	 => $row['name'],
				'source' => $row['id']
			));
		}
	}
	// Successful and return results
	exit(json_encode(array(
		'status'	 => 1,
		'message'	 => 'Success',
		'data'		 => $result
	)));
}
// Fail if variables is invalid
exit(json_encode(array(
	'status'	 => 0,
	'message'	 => 'Bad Request'
)));