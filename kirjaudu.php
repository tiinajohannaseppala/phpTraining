<?php 
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Kirjaudu sisään</title>
        <meta name="description" content="PHP-ohjelmointi">
        <meta name="author" content="Tiina Seppälä">
    </head>
	<style>
		td, th {
			text-align: left;
			padding: 4px;
		}
	</style>
    <body>
	
<!-- ohjaa tarkista_tunnus.php sivulle tarkistamaan käyttäjätietojen olemassa olon -->
        <form method="POST" action="tarkista_tunnus.php" align="center">
			<h2>Kirjaudu sisään</h2>
			<div align="center">
		<?php 
//  Tunnus jäi tyhjäksi
			if (isset($_SESSION['tunnus_puuttuu'])) {
				echo $_SESSION['tunnus_puuttuu']; 
				unset($_SESSION['tunnus_puuttuu']);
			}
//  Salasana jäi tyhjäksi
			if (isset($_SESSION['salasana_puuttuu'])) {
				echo $_SESSION['salasana_puuttuu']; 
				unset($_SESSION['salasana_puuttuu']);
			}

//  Tunnusta ei löytynyt DB:stä
			if (isset($_SESSION['tunnusta_ei_olemassa'])) {
				echo $_SESSION['tunnusta_ei_olemassa']; 
				unset($_SESSION['tunnusta_ei_olemassa']);
			}
// Uusi käyttäjä luotiin onnistuneesti
			if (isset($_SESSION['uusi_kayttaja'])) {
				echo $_SESSION['uusi_kayttaja']; 
				unset($_SESSION['uusi_kayttaja']);
			}
//	Kirjauduit ulos käyttöjärjestelmästä
			if (isset($_SESSION['uloskirjautuminen'])) {
				echo $_SESSION['uloskirjautuminen']; 
				unset($_SESSION['uloskirjautuminen']);
			}
		?>
			</div>
			<h4>
			<table align="center">
				<tr><td>Käyttäjätunnus:</td><td><input type="text" name="tunnus"></td></tr>
				<tr><td>Salasana:</td><td><input type="text" name="salasana"></td></tr>
			</table>
			</h4>
			<input type="submit" name="kirjaudu" value="Kirjaudu sisään">
        </form>
		<br>
		<br>
		<form method="POST" action="uusi_kayttaja.php">
			<h5>
			<table align="center">
				<tr><td>Eikö sinulla ole vielä tunnuksia?</td><td><input type="submit" name="uusi" value="Luo uusi käyttäjä"></td></tr>
			</table>
			</h5>
        </form>
	</body> 
</html>