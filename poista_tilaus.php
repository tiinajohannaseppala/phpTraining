<?php 
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Poista tilaus</title>
	<meta name="description" content="PHP-ohjelmointi">
	<meta name="author" content="Tiina Seppälä">
</head>
<body>
<?php
// kaikki sivun tiedot näkyvät vain kirjautuneelle, joten kaiken sisällön tulee olla if(isset($_SESSION["kirjautunut"])) sisällä
	if(isset($_SESSION["kirjautunut"])) {
		echo "Kirjautunut: " . $_SESSION["kirjautunut"] . "<br>";
// Tuodaan käyttäjän tiedot arrayssä sivulle ja tallennetaan muuttujiin
		if (isset($_SESSION['kirjautuneen_tiedot'])) { 
			$nimi_kayttaja = $_SESSION['kirjautuneen_tiedot'][1];
		}
// Tuodaan teto tilauksen avaimesta linkin ja getin avulla
		if (isset($_GET['avain'])) { 
			$avain = $_GET['avain'];
		}
//POISTA tilaus tietokannasta
		$con = mysqli_connect("localhost","root", "", "FIRMA"); 
		$poisto = "DELETE FROM tilaukset WHERE avain='$avain' AND tilaaja='$nimi_kayttaja'";
		$vastaus = mysqli_query($con,$poisto);
// Tarkistetaan onnistuiko poisto
		if (!$vastaus) {										
			echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
			mysqli_close($con);
		}
// Tulostetaan etusivulle tieto onnistumisesta
		else {
			mysqli_close($con);
			$tilauksen_poisto = "Tilaus poistettiin onnistuneesti";
			$_SESSION["tilauksen_poisto"] = $tilauksen_poisto;
			?><script>location.replace("etusivu.php");</script><?php
		}
?>	
<!-- ULOSKIRJAUTUMINEN vie omalle sivulle kirjaudu_ulos.php-->
	<br><br><br><br>
	<form action="kirjaudu_ulos.php" method="POST"><input type="submit" name="ulos" value="Kirjaudu ulos"</input></form>
	
<!-- if(isset($_SESSION["kirjautunut"])) lopetus
<?php
		}
			else { 
				echo "Olet kirjautunut ulos järjestelmästä. Sinun tulee <a href='kirjaudu.php'>kirjautua sisään</a> nähdäksesi sivun.";
			}
?>
	</body> 
</html>