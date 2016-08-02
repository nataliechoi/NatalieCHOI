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
// AJAX is creating comment
if ( !is_null(filter_input(INPUT_GET, 'create')) ) {
	$video_id	 = filter_input(INPUT_POST, 'video_id');
	$content	 = filter_input(INPUT_POST, 'comment');
	if ( // Check if POST variables is valid
		!is_null($video_id) && !empty($video_id) &&
		!is_null($content) && !empty($content)
	) {
		$query		 = $db->prepare('INSERT INTO video_message (video_id, user_id, content, create_time) VALUES (?, ?, ?, ?)');
		$query->execute(array($video_id, $_SESSION['user']['id'], $content, time()));
		// Successful
		exit(json_encode(array(
			'status'	 => 1,
			'message'	 => 'Success',
			'data'		 => NULL
		)));
	}
}
// Fail if variables is invalid
exit(json_encode(array(
	'status'	 => 0,
	'message'	 => 'Bad Request'
)));
