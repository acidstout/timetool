<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>TimeToolWrapper class demo</title>
		<link rel="stylesheet" href="css/demo.css"/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" defer></script>
		<script src="js/demo.js" defer></script>
	</head>
	<body>
		<div class="fixed-center">
			<h2>Login</h2>
			<div id="login">
				<form action="#" method="post" autocomplete="off">
					<input type="password" style="display:none;" name="disable-autocomplete-1" value="disable-autocomplete-1"/>
					<input type="password" style="display:none;" name="disable-autocomplete-2" value="disable-autocomplete-2"/>
					<input type="text" id="user" required value="<?php echo isset($username) ? $username : null;?>" autocomplete="off" placeholder="Username"/>
					&nbsp;<input type="password" id="pass" required value="<?php echo isset($password) ? $password : null;?>" autocomplete="off" placeholder="Password"/>
					&nbsp;<button type="button" id="submit">Login</button>
				</form>
				<div id="result"></div>
			</div>
		</div>
	</body>
</html>