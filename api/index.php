<?php
session_start(); // Enable session

// Check if it is an AJAX request
$request = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');
if (empty($request) || strtolower($request) !== 'xmlhttprequest') {
	// If not AJAX then return Forbidden
	exit(json_encode(array(
		'status'	 => 0,
		'message'	 => 'Forbidden'
	)));
}

// Connect to database
$conn	 = "mysql:host=localhost;dbname=natalie";
$db		 = new PDO($conn, 'natalie', 'Natalie1!');
$db->exec("SET NAMES 'utf8';");
