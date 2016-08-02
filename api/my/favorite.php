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
// AJAX is creating favorite item
if ( !is_null(filter_input(INPUT_GET, 'create')) ) {
	$type		 = filter_input(INPUT_POST, 'type');
	$source_id	 = filter_input(INPUT_POST, 'source_id');
	if ( // Check if POST variables is valid
		!is_null($type) && !empty($type) && 
		!is_null($source_id) && !empty($source_id)
	) {
		$query = $db->prepare('SELECT * FROM favorite WHERE user_id = ? AND source_id = ? AND type = ?');
		$query->execute(array($_SESSION['user']['id'], $source_id, $type));
		// Check if favorite list already has marked
		if ( $query->rowCount() === 0 ) {
			$query = $db->prepare('INSERT INTO favorite (user_id, source_id, type, create_time) VALUES (?, ?, ?, ?)');
			$query->execute(array($_SESSION['user']['id'], $source_id, $type, time()));
			// Successful
			exit(json_encode(array(
				'status'	 => 1,
				'message'	 => 'Success',
				'data'		 => NULL
			)));
		} else {
			// Fail if favorite list already has marked
			exit(json_encode(array(
				'status'	 => 0,
				'message'	 => 'Already exist in favorite list'
			)));
		}
	}
} 
// AJAX is removing favorite item
else if ( !is_null(filter_input(INPUT_GET, 'remove')) ) {
	// Check if request is using DELETE method
	if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'DELETE') {
		$data = array();
		parse_str(file_get_contents("php://input"), $data);
		// Check if DELETE variables is valid
		if(!is_null($data) && !empty($data) && isset($data['favorites'])) {
			$remove_ids = $data['favorites'];
			// Remove from favorite list one by one
			foreach ( $remove_ids as $remove_id ) {
				$query = $db->prepare('DELETE FROM favorite WHERE id = ?');
				$query->execute(array($remove_id));
			}
			// Successful
			exit(json_encode(array(
				'status'	 => 1,
				'message'	 => 'Success',
				'data'		 => NULL
			)));
		}
	}
} 
// AJAX is listing all favorite items
else {
	$query		 = $db->prepare('SELECT id, source_id, type, create_time FROM favorite WHERE user_id = ? ORDER BY create_time DESC');
	$query->execute(array($_SESSION['user']['id']));
	$favorites	 = array();
	foreach ( $query->fetchAll(PDO::FETCH_ASSOC) as $favorite ) {
		$subQuery = $db->prepare("SELECT * FROM {$favorite['type']} WHERE id = ?");
		$subQuery->execute(array($favorite['source_id']));
		// Analysis the type of favorite source
		switch ($favorite['type']) {
			case 'drug':
				$type	 = 'Drug Information';
				break;
			case 'tnr':
				$type	 = 'Treatment & Rehabilitation';
				break;
			case 'seekhelp':
				$type	 = 'Seek Help';
				break;
			case 'activity':
				$type	 = 'Anti-drug Activities';
				break;
			case 'video':
				$type	 = 'Anti-drug Videos';
				break;
		}
		$source						 = $subQuery->fetch(PDO::FETCH_ASSOC);
		$favorites[$favorite['id']]	 = array(
			'name'	 => $source['name'],
			'type'	 => $type,
			'link'	 => "{$favorite['type']}-info",
			'source' => $source['id'],
			'time'	 => date('m/d H:i', $favorite['create_time'])
		);
	}
	// Successful and return latest favorite list
	exit(json_encode(array(
		'status'	 => 1,
		'message'	 => 'Success',
		'data'		 => $favorites
	)));
}
// Fail if anything error
exit(json_encode(array(
	'status'	 => 0,
	'message'	 => 'Bad Request'
)));
