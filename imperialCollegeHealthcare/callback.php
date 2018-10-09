<?php
	//header 
	$pageTitle = "Memorable Info Registration";
	require "inc/header.php";

	//random numbers for secret code
	$numRange = range(1, 8);
	if (!Session::exists('now')) {
		shuffle($numRange);
		Session::put('numRange', $numRange);
		Session::put('now', date("Y-m-d H:i:s"));
	} else {
		if (strtotime(Session::get('now')) < time() - (2.5*60)) {
			Session::delete('now');
			Session::delete('numRange');
			header("Refresh:0");
			shuffle($numRange);
			Session::put('numRange', $numRange);
			Session::put('now', date("Y-m-d H:i:s"));
		}
	}
	$chosenNumRange = array($_SESSION['numRange'][1], $_SESSION['numRange'][3], $_SESSION['numRange'][4], $_SESSION['numRange'][7]);
	sort($chosenNumRange);
	function ordinal($number) {
	    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
	    if ((($number % 100) >= 11) && (($number%100) <= 13))
	        return $number. 'th';
	    else
	        return $number. $ends[$number % 10];
	}


	//form submission
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		//register
		if (isset($_POST['submitRegistration'])) {
			if(Token::check($_POST['tokenRegister'], 'tokenRegister')) {
				$name = $_POST['name'];
				$email = $userInfo['name'];
				$secret01 = $_POST['secret01'];
				$secret02 = $_POST['secret02'];

				if(!empty($name) && !empty($secret01) && !empty($secret02)) {
					if (strlen($name) < 5 || strlen($name) > 25) {
						$error = "Please enter a name that is longer than 5 characters and less than 25";
					} elseif ($secret01 !== $secret02) {
						$error = "Please enter matching secret codes that you will remember.";
					} elseif(strlen($secret01) < 9) {
						$error = "Please enter a secret code which has 9 characters or more.";
					} else {
						$userSql = "INSERT INTO user (auth0ID, name, email) VALUES (?, ?, ?)";
						$userQuery = $handler->prepare($userSql);
						$userQuery->execute(array($auth0ID, $name, $email));

						$lastInsertedID = $handler->lastInsertId();

						$secretArr = str_split(strtolower($secret01));
						for ($i=0; $i < count($secretArr); $i++) { 
							$charNo = $i + 1;
							$salt = Hash::salt(32);
							$hashedCharacter = Hash::make($secretArr[$i], $salt);
							$hashedSql = "INSERT INTO secretcode (characterNo, hashedCharacter, salt, userID) VALUES (?, ?, ?, ?)";
							$hashedQuery = $handler->prepare($hashedSql);
							$hashedQuery->execute(array($charNo, $hashedCharacter, $salt, $lastInsertedID));
						}
						header("Refresh:0");
					}
				} else {
					$error = "Please fill in all fields to complete registration.";
				}

			}
		}


		//login
		if (isset($_POST['submitLogin'])) {
			if(Token::check($_POST['tokenLogin'], 'tokenLogin')) {
				$secretCodeArrays = array();
				$secretSql = "SELECT * FROM secretcode WHERE userID = ? AND characterNo IN (?, ?, ?, ?)";
				$secretQuery = $handler->prepare($secretSql);
				$secretQuery->bindParam(1, $db_userID, PDO::PARAM_INT);
				$secretQuery->bindParam(2, $chosenNumRange[0], PDO::PARAM_INT);
				$secretQuery->bindParam(3, $chosenNumRange[1], PDO::PARAM_INT);
				$secretQuery->bindParam(4, $chosenNumRange[2], PDO::PARAM_INT);
				$secretQuery->bindParam(5, $chosenNumRange[3], PDO::PARAM_INT);
				$secretQuery->execute();

				while ($row = $secretQuery->fetch(PDO::FETCH_ASSOC)) {
					$secretCodeArrays[] = array($row['characterNo'], $row['hashedCharacter'], $row['salt']);
				}

				if ($secretCodeArrays[0][1] == Hash::make($_POST['secretCode01'], $secretCodeArrays[0][2]) && $secretCodeArrays[1][1] == Hash::make($_POST['secretCode02'], $secretCodeArrays[1][2]) && $secretCodeArrays[2][1] == Hash::make($_POST['secretCode03'], $secretCodeArrays[2][2]) && $secretCodeArrays[3][1] == Hash::make($_POST['secretCode04'], $secretCodeArrays[3][2])) {
					$loginSql = "INSERT INTO login (loginResult, loginTime, userID) VALUES (?, ?, ?)";
					$loginQuery = $handler->prepare($loginSql);
					$loginQuery->execute(array('success', date("Y-m-d H:i:s"), $db_userID));
					Session::put('user', $db_userID);
					Session::put('accessTimeDate', date("Y-m-d H:i:s"));
					Redirect::to('home.php');
				} else {
					$loginSql = "INSERT INTO login (loginResult, loginTime, userID) VALUES (?, ?, ?)";
					$loginQuery = $handler->prepare($loginSql);
					$loginQuery->execute(array('fail', date("Y-m-d H:i:s"), $db_userID));

					$error = "Sorry, you've entered the wrong characters. Please try again.";
				}

			}
		}
	}

	//if user not identified
	if (!isset($auth0ID)) {
		Redirect::to('http://imperialcollegehealthcare.com/');
	} else {
		//finding if user exists or not
		if(isset($db_auth0ID)) {

			//display login form if user does exist in local database
			$loginForm = '<p>Secret Code Login</p>';
			if (isset($error)) {
				$loginForm .= '<p>' . $error . '</p>';
			}
			$loginForm .= '<form method="POST" name="secretCodeLogin">';

			$loginForm .= '<fieldset>';
			$loginForm .= '<label for="secretCode01">' . ordinal($chosenNumRange[0]) . '</label>';
			$loginForm .= '<select id="secretCode01" name="secretCode01">';
			foreach (range(0, 9) as $number) {
				$loginForm .= '<option>'. $number . '</option>';
			}
			foreach (range('a', 'z') as $letter) {
				$loginForm .= '<option>'. $letter . '</option>';
			}
			$loginForm .= '</select>';
			$loginForm .= '</fieldset>';



			$loginForm .= '<fieldset>';
			$loginForm .= '<label for="secretCode02">' . ordinal($chosenNumRange[1]) . '</label>';
			$loginForm .= '<select id="secretCode02" name="secretCode02">';
			foreach (range(0, 9) as $number) {
				$loginForm .= '<option>'. $number . '</option>';
			}
			foreach (range('a', 'z') as $letter) {
				$loginForm .= '<option>'. $letter . '</option>';
			}
			$loginForm .= '</select>';
			$loginForm .= '</fieldset>';



			$loginForm .= '<fieldset>';
			$loginForm .= '<label for="secretCode03">' . ordinal($chosenNumRange[2]) . '</label>';
			$loginForm .= '<select id="secretCode03" name="secretCode03">';
			foreach (range(0, 9) as $number) {
				$loginForm .= '<option>'. $number . '</option>';
			}
			foreach (range('a', 'z') as $letter) {
				$loginForm .= '<option>'. $letter . '</option>';
			}
			$loginForm .= '</select>';
			$loginForm .= '</fieldset>';


			$loginForm .= '<fieldset>';
			$loginForm .= '<label for="secretCode04">' . ordinal($chosenNumRange[3]) . '</label>';
			$loginForm .= '<select id="secretCode04" name="secretCode04">';
			foreach (range(0, 9) as $number) {
				$loginForm .= '<option>'. $number . '</option>';
			}
			foreach (range('a', 'z') as $letter) {
				$loginForm .= '<option>'. $letter . '</option>';
			}
			$loginForm .= '</select>';
			$loginForm .= '</fieldset>';

			$loginForm .= '<input type="hidden" name="tokenLogin" value="' . Token::generate('tokenLogin') . '">';
			$loginForm .= '<input type="submit" name="submitLogin" value="Log In">';
			$loginForm .= '</form>';


			//checking login attemps before displaying form, some variables made in header.php
			function isUserAllowedToAcess2ndLoginForm() {
				global $loginAttempts;
				global $loginForm;
				$loginAttempts = array_slice($loginAttempts, -5);
				if (count($loginAttempts) > 4) {
					if ($loginAttempts[0][0] === 'fail' && $loginAttempts[1][0] === 'fail' && $loginAttempts[2][0] === 'fail'&& $loginAttempts[3][0] === 'fail'&& $loginAttempts[4][0] === 'fail') {

						$failedLoginTime = new DateTime($loginAttempts[4][1]);
						$failedLoginTime->modify("+12 hours");

						//https://stackoverflow.com/questions/5906686/php-time-remaining-until-specific-time-from-current-time-of-page-load
						$now = new DateTime();
						$future_date = new DateTime($loginAttempts[4][1]);
						$interval = $failedLoginTime->diff($now);
						$remainingTime = $interval->format("%h hours %i minutes %s seconds");


						$failedDateTime = new DateTime($loginAttempts[4][1]);
						$failedDateTime->modify("+12 hours");
						$failedDateTimePlus12Hours = $failedDateTime->format("Y-m-d H:i:s");

						if (date("Y-m-d H:i:s") < $failedDateTimePlus12Hours) {
							echo "You've had too many failed attempts to log in. You have <strong>" . $remainingTime . "</strong> till you can try again.";
						} else {
							echo $loginForm;
						}

					} else {
						echo $loginForm;
					}
				} else {
					echo $loginForm;
				}
			}


			//check last login time to see if 2nd login is required or not, some variables made in header.php
			if (isset($loginAttemptsSuccess[0][0])) {
				$lastSucessLoginTime = $loginAttemptsSuccess[0][1];

				$now = new DateTime(date("Y-m-d H:i:s"));
				$now->modify("-24 hours");
				$nowMinus24Hours = $now->format("Y-m-d H:i:s");

				if ($lastSucessLoginTime < $nowMinus24Hours) {
					 isUserAllowedToAcess2ndLoginForm();
				} else {
					Session::put('user', $db_userID);
					Session::put('accessTimeDate', date("Y-m-d H:i:s"));
					Redirect::to('home.php');
				}
			} else {
				 isUserAllowedToAcess2ndLoginForm();
			}


		} else { 
			//display registration form if user does exist in local database
			$registrationForm = '<p>Secret Code Registration</p>';
			if (isset($error)) {
				$registrationForm .= '<p>' . $error . '</p>';
			}
			$registrationForm .= '<form method="POST" name="secretCodeRegistration">';
			$registrationForm .= '<input type="text" name="name" placeholder="Name..">';
			$registrationForm .= '<input type="password" name="secret01" placeholder="Secret Code..">';
			$registrationForm .= '<input type="password" name="secret02" placeholder="Repeat Secret Code..">';
			$registrationForm .= '<input type="hidden" name="tokenRegister" value="' . Token::generate('tokenRegister') . '">';
			$registrationForm .= '<input type="submit" name="submitRegistration" value="Finish Registration">';
			$registrationForm .= '</form>';

			echo $registrationForm;
		}

	}



	//footer
	require "inc/footer.php";

	// var_dump($_SESSION);
?>









