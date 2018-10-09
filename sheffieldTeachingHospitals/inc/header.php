<?php
	require_once 'core/init.php';

	//User Info from Auth0
	try {
		$userInfo = $auth0->getUser();
	} catch (Exception $e) {
		// $e->getMessage();
		header('Location: http://sheffieldteachinghospitals.com/');
		exit();
	}

	//user Auth0 ID
	$fullAuth0ID = explode("|", $userInfo["sub"]);
	$auth0ID = $fullAuth0ID[1];

	//user info from database
	$auth0IDSql = "SELECT userID, auth0ID FROM user WHERE auth0ID = ?";
	$auth0IDQuery = $handler->prepare($auth0IDSql);
	$auth0IDQuery->bindParam(1, $auth0ID, PDO::PARAM_STR);
	$auth0IDQuery->execute();

	while ($row = $auth0IDQuery->fetch(PDO::FETCH_ASSOC)) {
		$db_auth0ID = $row['auth0ID'];
		$db_userID = $row['userID'];

	}


	//getting user logging attempts		
	$loginAttempts = array();
	$loginsSql = "SELECT * FROM login WHERE userID = ?";
	$loginsQuery = $handler->prepare($loginsSql);
	$loginsQuery->bindParam(1, $db_userID, PDO::PARAM_INT);
	$loginsQuery->execute();

	while ($row = $loginsQuery->fetch(PDO::FETCH_ASSOC)) {
		$loginAttempts[] = array($row['loginResult'], $row['loginTime']);
	}


	//user login attemps that were successful
	$loginAttemptsSuccess = array();
	$reversedLoginAttempts = array_reverse($loginAttempts);
	for ($i=0; $i < count($reversedLoginAttempts); $i++) { 
		if ($reversedLoginAttempts[$i][0] === 'success') {
			$loginAttemptsSuccess[] = array($reversedLoginAttempts[$i][0], $reversedLoginAttempts[$i][1]);
		}
	}


	//Log Out
	if(isset($_POST['logout'])) {
		header('Location: https://wjps.eu.auth0.com/v2/logout?returnTo=http%3A%2F%2Fsheffieldteachinghospitals.com/logout.php');
		exit;
	}


?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $pageTitle ?></title>
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
	</head>

	<body>
		<header>
			<h1><a href="callback.php">WJPS</a></h1>
		</header>

		<section>

			<?php
				$logoutForm = '<form method="POST" name="logoutForm">';
				$logoutForm .= '<input type="submit" name="logout" value="Logout">';
				$logoutForm .= '</form>';
			?>
			