<?php

//Auth0: provided on Auth0 website
require 'vendor/autoload.php';
use Auth0\SDK\Auth0;

$auth0 = new Auth0([
  'domain' => 'wjps.eu.auth0.com',
  'client_id' => 'UTl17U4q16p8n5CsQsyyfjmKTFKqueaR',
  'client_secret' => 't7zH5P0BqEsbhYlXfQfWCDOA18RXeMkVSmF_CRf6kOzjEYxj7lVWn4Vy_uzgAHVP',
  'redirect_uri' => 'http://imperialcollegehealthcare.com/callback.php', //update to https
  'audience' => 'https://wjps.eu.auth0.com/userinfo',
  'scope' => 'openid profile',
  'persist_id_token' => true,
  'persist_access_token' => true,
  'persist_refresh_token' => true,
]);


//database connection
try {
	$handler = new PDO('mysql:host=127.0.0.1;dbname=wjps', 'root', '');
	$handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Sorry, there has been an error.");
}


//required classes
require_once 'interfaces/sso.php';
require_once 'classes/sso.php';
require_once 'classes/redirect.php';
require_once 'classes/session.php';
require_once 'classes/token.php';
require_once 'classes/hash.php';


// single sign on
$SSO = new SSO();