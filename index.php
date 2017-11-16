<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>TimeToolWrapper class demo</title>
		<link rel="stylesheet" href="css/demo.css"/>
		<script src="js/jquery.min.js" defer></script>
		<script src="js/demo.js" defer></script>
	</head>
	<body>
		<div class="fixed-center">
			<h2>Token generieren</h2>
			<div id="login">
				<form action="#" method="post" autocomplete="off">
					<input type="password" style="display:none;" name="disable-autocomplete-1" value="disable-autocomplete-1"/>
					<input type="password" style="display:none;" name="disable-autocomplete-2" value="disable-autocomplete-2"/>
					<input type="text" id="user" required value="" autocomplete="off" placeholder="Benutzername"/>
					&nbsp;<input type="password" id="pass" required value="" autocomplete="off" placeholder="Passwort"/>
					&nbsp;<button type="button" id="submit">Generieren</button>
				</form>
				<div id="result"><?php
					global $expired;
					global $demokey;
					echo (isset($demokey) && $demokey) ? 'Bitte geheimen Schl&uuml;ssel &auml;ndern!' : null;
					echo (isset($expired) && $expired) ? 'Ihr Token ist abgelaufen.' : null;
				?></div>
			</div>
		</div>
	</body>
</html>