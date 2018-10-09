<?php
	// header 
	$pageTitle = "Home - Sheffield Teaching Hospitals";
	require "inc/header.php";
	require "inc/page.php";

	echo $logoutForm;

	$user = Session::get('user');
	$userSQL = "SELECT * FROM user WHERE userID = ?";
	$userQuery = $handler->prepare($userSQL);
	$userQuery->bindParam(1, $user, PDO::PARAM_INT);
	$userQuery->execute();

	while ($row = $userQuery->fetch(PDO::FETCH_ASSOC)) {
		echo $row['auth0ID'] . '<br>';
		echo $row['name'] . '<br>';
		echo $row['email'] . '<br>';
	}

	require "inc/footer.php";
?>