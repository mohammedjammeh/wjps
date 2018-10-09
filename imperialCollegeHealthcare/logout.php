<?php
	require "inc/header.php";
	session_start();
	unset($_SESSION['auth0__access_token'], $_SESSION['auth0__id_token'], $_SESSION['auth0__refresh_token'], $_SESSION['auth0__user']);


	$userID = Session::get('user');
	$accessOutTimeDate = date("Y-m-d H:i:s");
	$logOutSql = "UPDATE activity SET accessOutTimeDate = ? WHERE userID = $userID AND accessOutTimeDate IS NULL";
	$logOutQuery = $handler->prepare($logOutSql);
	$logOutQuery->execute(array($accessOutTimeDate));

	Session::delete('user');
	Session::delete('logInTimeDate');


	Redirect::to('http://imperialcollegehealthcare.com/');
	exit;
?>
