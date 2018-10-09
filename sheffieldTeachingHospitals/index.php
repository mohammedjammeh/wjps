<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Sheffield Teaching Hospitals</title>

		<!-- jQuery: this code allows browsers to ensure that resources hosted on third-party servers have not been tampered with. From http://code.jquery.com -->
		<script
		  src="http://code.jquery.com/jquery-3.3.1.min.js"
		  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		  crossorigin="anonymous">
  		</script>


  		<!-- Auth0: this code is an installation method for using auth0.js to make it easier to do authentication and authorisation with Auth0. From https://auth0.com/docs/libraries/auth0js/v9#installation-options -->
		<script src="https://cdn.auth0.com/js/auth0/9.2.2/auth0.min.js"></script>


		<!-- Initializing Script -->
	    <script>
	        $(document).ready(function() {
	          var webAuth = new auth0.WebAuth({
	            domain: 'wjps.eu.auth0.com',
	            clientID: 'zBdJmHGV2gSk1VeGFb1BN52ijwzJYgBC',
	            redirectUri: 'http://sheffieldteachinghospitals.com/callback.php', //update to https on here and on auth0 settings
	            audience: `https://wjps.eu.auth0.com/userinfo`,
	            responseType: 'code',
	            scope: 'openid profile'
	          });

	          $('#login').click(function(e) {
	            e.preventDefault();
	            webAuth.authorize();
	          });
	        });
	    </script>
	</head>

	<body>
        <div>
            <button id="login">Sign Into Sheffield Teaching Hospitals with Auth0</button>
        </div>
	</body>
</html>





