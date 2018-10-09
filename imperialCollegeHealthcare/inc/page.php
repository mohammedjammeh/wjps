<?php
	//Unset user session if previous login is longer than 24hours
	if (isset($loginAttemptsSuccess[0][0])) {
		$lastSucessTime = $loginAttemptsSuccess[0][1];

		$currentTime = new DateTime(date("Y-m-d H:i:s"));
		$currentTime->modify("-24 hours");
		$nowTakeAway24Hours = $currentTime->format("Y-m-d H:i:s");

		if ($lastSucessTime < $nowTakeAway24Hours) {
			 Session::delete('user');
		}
	}


	// Redirect user if session is not set
	if(!Session::exists('user')) {
		Redirect::to('callback.php');
	}


	//activity variables
	$accessTimeDate = Session::get('accessTimeDate');
	$urlAccessed = $_SERVER['REQUEST_URI'];
	$urlAccessedTimeDate = date("Y-m-d H:i:s");
	$userID = Session::get('user');
	$websiteName = 'imperial';


	//sso logout check depending if user has logged out from another application
	$ssoLogOutThisApp = array();
	$ssoLogOutOtherApps = array();
	$ssoLogoutCheckSQL = "SELECT * FROM activity WHERE userID = ?";
	$ssoLogoutCheckQuery = $handler->prepare($ssoLogoutCheckSQL);
	$ssoLogoutCheckQuery->bindParam(1, $userID, PDO::PARAM_INT);
	$ssoLogoutCheckQuery->execute();

	while ($row = $ssoLogoutCheckQuery->fetch(PDO::FETCH_ASSOC)) {
		if ($row['website'] === $websiteName) {
			$ssoLogOutThisApp[] = $row['accessOutTimeDate'];
		} else {
			$ssoLogOutOtherApps[] = $row['accessOutTimeDate'];
		}
	}
	$reversedSSOLogOutThisApp = array_reverse($ssoLogOutThisApp);
	$reversedSSOLogOutOtherApps = array_reverse($ssoLogOutOtherApps);

	if (isset($reversedSSOLogOutOtherApps[0]) && isset($reversedSSOLogOutThisApp)) {
		if ($reversedSSOLogOutOtherApps[0] !== NULL) {
		if ($reversedSSOLogOutThisApp[0] !== $reversedSSOLogOutOtherApps[0]) {
			header('Location: https://wjps.eu.auth0.com/v2/logout?returnTo=http%3A%2F%2Fimperialcollegehealthcare.com/logout.php');
			exit;
		}
	}
	}

		


	//record user
	$activitySql = "INSERT INTO activity (accessTimeDate, urlAccessed, urlAccessedTimeDate, website, userID) VALUES (?, ?, ?, ?, ?)";
	$activityQuery = $handler->prepare($activitySql);
	$activityQuery->execute(array($accessTimeDate, $urlAccessed, $urlAccessedTimeDate, $websiteName, $userID));

?>