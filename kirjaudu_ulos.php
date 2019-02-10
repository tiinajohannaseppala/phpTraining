<?php 
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Kirjaudu ulos</title>
        <meta name="description" content="PHP-ohjelmointi">
        <meta name="author" content="Tiina Seppälä">
    </head>
    <body>
<?php
// Luo session uloskirjautuminen ja vie käyttäjän kirjautumissivulle
// + Tuhoaa sessiot: kirjautunut ja kirjautuneen tiedot
	$uloskirjautuminen = "Uloskirjautuminen onnistui.";
	$_SESSION["uloskirjautuminen"] = $uloskirjautuminen;
	
	unset($_SESSION["kirjautunut"]);
	unset($_SESSION["kirjautuneen_tiedot"]);
	?><script> location.replace("kirjaudu.php"); </script><?php

?>
	</body> 
</html>

