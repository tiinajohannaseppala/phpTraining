<?php 
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Tarkasta käyttäjätunnukset</title>
	<meta name="description" content="PHP-ohjelmointi">
	<meta name="author" content="Tiina Seppälä">
</head>
<body>
<?php		
// KIRJAUDU-nappia painettiin / tarkistaan löytyykö käyttäjätunnusta ja salasanaa DB:stä
	if (isset($_POST["kirjaudu"])) {
		$tunnus = $_POST["tunnus"]; 
		$salasana = $_POST["salasana"];
// tarkistetaan että molemmat tiedot on annettu	
		if (strlen($tunnus) < 1) {
			$tunnus_puuttuu = "Käyttäjätunnusta ei syötetty.<br>";
			$_SESSION["tunnus_puuttuu"] = $tunnus_puuttuu;
			?><script>location.replace("kirjaudu.php");</script><?php
		}
		if (strlen($salasana) < 1) {
			$salasana_puuttuu = "Salasanaa ei syötetty.<br>";
			$_SESSION["salasana_puuttuu"] = $salasana_puuttuu;
			?><script>location.replace("kirjaudu.php");</script><?php
		}
//tehdään haku KAYTTAJA-tietokannasta
		if (strlen($salasana) >= 1 AND strlen($tunnus) >= 1) {
			$conn = mysqli_connect("localhost","root", "", "KAYTTAJA"); 
			$haku = "SELECT avain, nimi, tunnus, salasana, puh, email, kayntios, laskutusos, as_avain, pinta_ala, tontin_pinta_ala FROM asiakas WHERE tunnus='$tunnus' AND salasana='$salasana'";
			$vastaus = mysqli_query($conn,$haku);
// Jos haussa tapahtui virhe
			if (!$vastaus) {
				echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
				mysqli_close($conn);
			}
// Jos haulla ei löytynyt yhtään tulosta
			elseif (mysqli_num_rows($vastaus) == 0) { 
				mysqli_close($conn);
				$tunnusta_ei_olemassa = "Käyttäjätunnus tai salasana on virheellinen. Kokeile kirjautua uudelleen.";
				$_SESSION["tunnusta_ei_olemassa"] = $tunnusta_ei_olemassa;
				?><script>location.replace("kirjaudu.php");</script><?php
			}
// Tulostetaan tieto onnistumisesta
			if ($vastaus) {
				while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) {
					$avain = $rivi["avain"]; 
					$nimi = $rivi["nimi"];
					$tunnus = $rivi["tunnus"]; 
					$puh = $rivi["puh"];
					$email = $rivi["email"]; 
					$kayntios = $rivi["kayntios"]; 
					$laskutusos = $rivi["laskutusos"]; 
					$as_avain = $rivi["as_avain"]; 
				}
				mysqli_close($conn);
//Kirjautumisen linkki tulee tästä
				$kirjautunut = "<a href='muokkaa.php?tunnus=$tunnus'><b>$tunnus</b></a>";
				$_SESSION["kirjautunut"] = $kirjautunut;
// Tallennetaan käyttäjän kaikki tiedot sessioon >> käytetään työtilauksessa (voisi käyttää muokkaa sivullakin, muokkaa jos jää aikaa)
				$kirjautuneen_tiedot = array($avain,$nimi,$tunnus,$puh,$email,$kayntios,$laskutusos,$as_avain);
				$_SESSION["kirjautuneen_tiedot"] = $kirjautuneen_tiedot;
				?><script>location.replace("etusivu.php");</script><?php
			}
		}
	}
?>
</body> 
</html>