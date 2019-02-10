<?php 
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Muokkaa tietoja</title>
	<meta name="description" content="PHP-ohjelmointi">
	<meta name="author" content="Tiina Seppälä">
	<style>
		td, th {text-align: left; padding: 4px;}
	</style>
</head>
<body>
<?php
// kaikki sivun tiedot näkyvät vain kirjautuneelle, joten kaiken sisällön tulee olla if(isset($_SESSION["kirjautunut"])) sisällä
	if(isset($_SESSION["kirjautunut"])) {
				echo "Kirjautunut: " . $_SESSION["kirjautunut"] . "<br>";	
//Hakee MUOKATTAVAN käyttäjän tiedot tietokannasta
		if (isset($_GET["tunnus"])) {
			$tunnus = $_GET["tunnus"];
			$conn = mysqli_connect("localhost","root", "", "KAYTTAJA");
			$muokattava = "SELECT avain, nimi, tunnus, puh, email, kayntios, laskutusos, as_avain FROM asiakas WHERE tunnus = '$tunnus'"; 
			$vastaus = mysqli_query($conn,$muokattava); // suoritetaan kysely
// Tarkistetaan onnistuiko haku
			if (!$vastaus) {										
				echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
				mysqli_close($conn);
			}
// Tulostetaan tieto onnistumisesta >> muuttujia tarvitaan tiedon tulostamisessa FORMIIN
			else {
				while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) {
					$avain = $rivi["avain"]; 
					$nimi = $rivi["nimi"]; 
					$tunnus = $rivi["tunnus"];
					$puh = $rivi["puh"]; 
					$email = $rivi["email"];
					$k_osoite = $rivi["kayntios"]; 
					$l_osoite = $rivi["laskutusos"]; 
					$rakennustyypinnumero = $rivi["as_avain"]; //numero
				}
				mysqli_close($conn);
			}
		}
?>
		<h2>Muokkaa tietoja</h2>		
		<form method="post" action="">
			<h4>Pakolliset tiedot:</h4>
			<table>
				<input type="hidden" name="avain" value="<?php if (isset($avain)) {echo $avain;} ?>">
				<input type="hidden" name="tunnus" value="<?php if (isset($tunnus)) {echo $tunnus;} ?>">
				<tr><td>Nimi:				</td><td><input type="text" name="nimi" value="<?php if (isset($nimi_f)) {echo $nimi_f;} else {echo $nimi;}?>"></td></tr>
				<tr><td>Puhelinnumero:		</td><td><input type="text" name="puh" value="<?php if (isset($puh_f)) {echo $puh_f;} else {echo $puh;}?>"></td></tr>
				<tr><td>Sähköpostiosoite:	</td><td><input type="text" name="email" value="<?php if (isset($email_f)) {echo $email_f;} else {echo $email;}?>"></td></tr>
				<tr><td>Käyntiosoite:		</td><td><input type="text" name="k_osoite" value="<?php if (isset($k_osoite_f)) {echo $k_osoite_f;} else {echo $k_osoite;} ?>"></td></tr>
				<tr><td>Laskutusosoite:		</td><td><input type="text" name="l_osoite" value="<?php  if (isset($l_osoite_f)) {echo $l_osoite_f;} else {echo $l_osoite;} ?>"></td></tr>
			</table>
			<h4>Vapaaehtoiset tiedot:</h4>
			<table>
				<tr><td>Rakennuksen tyyppi:				</td><td>		<select name="asunto">
<?php 
// hakee tietokannasta asuntotyypin ja tulostaa vaihtoehdoiksi dropdown-valikkoon
					$conn = mysqli_connect("localhost","root", "", "KAYTTAJA");
					$haku = "SELECT avain, selite FROM asuntotyyppi";
					$vastaus = mysqli_query($conn, $haku);
// jos käyttäjä muutti arvoa muokkauksen aika
					if (isset($_POST["asunto"])) {
						if ($_POST["asunto"] >= 1 AND $_POST["asunto"] <= 8) {
							while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) {
								$avain = $rivi["avain"];
								$selite = $rivi["selite"];
								if ($avain != $_POST["asunto"]) {
									echo "<option value='$avain'>$selite</option>";
								}
								if ($avain == $_POST["asunto"]){
									echo "<option value='$avain' selected='selected'>$selite</option>";
								}
							}
							echo "<option value='0'>poista valinta</option>";
							mysqli_close($conn);
						}
					}
// jos käyttäjä on tehnyt valinnan asuntotyypille (as_avain_kayttaja on numero)
					elseif ($rakennustyypinnumero >= 1 AND $rakennustyypinnumero <= 8) {
						while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) {
							$avain = $rivi["avain"];
							$selite = $rivi["selite"];
							if ($avain != $rakennustyypinnumero) {
								echo "<option value='$avain'>$selite</option>";
							}
							if ($avain == $rakennustyypinnumero){
								echo "<option value='$avain' selected='selected'>$selite</option>";
							}
						}
						echo "<option value='0'>poista valinta</option>";
						mysqli_close($conn);
					}
// Jos käyttäjä on valinnut asuntotyypin tulostuu sen valintaan
					else {
						echo "<option value='0' selected='selected'>Valitse</option>";
						while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) {
							$avain = $rivi["avain"];
							$selite = $rivi["selite"];
							echo "<option value='$avain'>$selite</option>";
							}
						mysqli_close($conn);
						}
	?>
					</select></td></tr>
				</table>
				<h4>Vaihda salasana:</h4>
				<table>
					<tr><td>Anna uusi salasana:			</td><td>		<input type="text" name="salasana1">	</td></tr>
					<tr><td>Varmista uusi salasana:		</td><td>		<input type="text" name="salasana2">	</td></tr>
				</table>

				<br>
				<input type="submit" name="tallenna" value="Tallenna">
				<input type="submit" name="peruuta" value="Peruuta">
			</form>
			<br>
	<?php
//PERUUTA-nappia painettiin > vie takaisin etusivulle
		if (isset($_POST["peruuta"])) {
			?><script> location.replace("etusivu.php"); </script><?php
		}
//TALLENNA-nappia painettiin
// Tallennetaan saadut tiedot uusiin muuttujiin 
		if (isset($_POST["tallenna"])) {
			$nimi_f = $_POST["nimi"];
			$salasana1_f = $_POST["salasana1"]; 
			$salasana2_f = $_POST["salasana2"];
			$puh_f = $_POST["puh"];
			$email_f = $_POST["email"];
			$k_osoite_f = $_POST["k_osoite"];
			$l_osoite_f = $_POST["l_osoite"];
			$as_avain_f = $_POST["asunto"];
//Tarkistaa että kaikki tiedot on syötetty
			if (strlen($nimi_f) < 1 or strlen($puh_f) < 1 or strlen($email_f) < 1 or strlen($k_osoite_f) < 1 or strlen($l_osoite_f) < 1) {
				echo "<b>Tietojen päivittäminen epäonnistui.</b> Tarkista, että kaikki pyydetyt tiedot on syötetty.";
			}
//Tarkistaa, että salasana syötettiin oikein molempiin ruutuihin
			if ($salasana1_f != $salasana2_f) {
				echo "<b>Virhe salasanaa päivitettäessä.</b> Tarkista, että antamasi uusi salasana on syötetty oikein molempiin kenttiin.";
			}
// PAITSI salasanat / kaikki tiedot syöytetty 
			if (strlen($nimi_f) >= 1 AND strlen($salasana1_f) == 0 AND strlen($salasana2_f) == 0 AND strlen($puh_f) >= 1 AND strlen($email_f) >= 1 AND strlen($k_osoite_f) >= 1 AND strlen($l_osoite_f) >= 1) {			
				$conn = mysqli_connect("localhost","root", "", "KAYTTAJA");
// PAITSI salasanat / JOS as_avain = 0 (ei valintaa tai valinta poistettu)
				if ($as_avain_f == 0) {
					$muokkaus = "UPDATE asiakas SET nimi='$nimi_f', puh='$puh_f', email='$email_f', kayntios='$k_osoite_f', laskutusos='$l_osoite_f', as_avain='0' WHERE tunnus='$tunnus'"; 
					$vastaus = mysqli_query($conn,$muokkaus);
					if ($vastaus) {
						mysqli_close($conn);
						$tietojen_paivitys1 = "Tietojen päivitys onnistui!";
						$_SESSION["tietojen_paivitys1"] = $tietojen_paivitys1;
						?><script> location.replace("etusivu.php"); </script><?php
					}
					else {									
						echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
						mysqli_close($conn);
					}
				}
// PAITSI salasanat / muutoin edetään tämän kaavan mukaan (as_avain != 0)
				else {
					$muokkaus = "UPDATE asiakas SET nimi='$nimi_f', puh='$puh_f', email='$email_f', kayntios='$k_osoite_f', laskutusos='$l_osoite_f', as_avain='$as_avain_f' WHERE tunnus='$tunnus'"; 
					$vastaus = mysqli_query($conn,$muokkaus);
					if ($vastaus) {
						$tietojen_paivitys1 = "Tietojen päivitys suoritettu onnistuneesti!";
						$_SESSION["tietojen_paivitys1"] = $tietojen_paivitys1;
// Tallennetaan käyttäjän kaikki tiedot sessioon >> käytetään työtilauksessa (voisi käyttää muokkaa sivullakin, muokkaa jos jää aikaa)
						$kirjautuneen_tiedot = array($avain,$nimi_f,$tunnus,$puh_f,$email_f,$k_osoite_f,$l_osoite_f,$as_avain_f);
						$_SESSION["kirjautuneen_tiedot"] = $kirjautuneen_tiedot;
						?><script> location.replace("etusivu.php"); </script><?php
					}
					else {									
						echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
					}
				}
			}
// MYÖS salasanat / kaikki tiedot syöytetty 
			if (strlen($nimi_f) >= 1 AND strlen($salasana1_f) >= 1 AND strlen($salasana2_f) >= 1 AND $salasana1_f == $salasana2_f AND strlen($puh_f) >= 1 AND strlen($email_f) >= 1 AND strlen($k_osoite_f) >= 1 AND strlen($l_osoite_f) >= 1) {
				$conn = mysqli_connect("localhost","root", "", "KAYTTAJA");
			// MYÖS salasanat / JOS as_avain = 0 (ei valintaa tai valinta poistettu)
				if ($as_avain == 0) {
					$muokkaus = "UPDATE asiakas SET nimi='$nimi_f', salasana='$salasana1_f', puh='$puh_f', email='$email_f', kayntios='$k_osoite_f', laskutusos='$l_osoite_f', as_avain='0' WHERE tunnus='$tunnus'"; 
					$vastaus = mysqli_query($conn,$muokkaus);
					if ($vastaus) {
						$tietojen_paivitys2 = "Tietojen päivitys ja salasanan vaihto suoritettu onnistuneesti!";
						$_SESSION["tietojen_paivitys1"] = $tietojen_paivitys2;
// Tallennetaan käyttäjän kaikki tiedot sessioon >> käytetään työtilauksessa (voisi käyttää muokkaa sivullakin, muokkaa jos jää aikaa)
						$kirjautuneen_tiedot = array($avain,$nimi_f,$tunnus,$puh_f,$email_f,$k_osoite_f,$l_osoite_f);
						$_SESSION["kirjautuneen_tiedot"] = $kirjautuneen_tiedot;
						?><script> location.replace("etusivu.php"); </script><?php
					}
					else {									
						echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
					}
				}
// MYÖS salasanat / muutoin edetään tämän kaavan mukaan (as_avain lisätään myös)
				else {
					$muokkaus = "UPDATE asiakas SET nimi='$nimi_f', salasana='$salasana1_f', puh='$puh_f', email='$email_f', kayntios='$k_osoite_f', laskutusos='$l_osoite_f', as_avain='$as_avain_f' WHERE tunnus='$tunnus'"; 
					$vastaus = mysqli_query($conn,$muokkaus);
					if ($vastaus) {
						$tietojen_paivitys2 = "Tietojen päivitys ja salasanan vaihto suoritettu onnistuneesti!";
						$_SESSION["tietojen_paivitys1"] = $tietojen_paivitys2;
// Tallennetaan käyttäjän kaikki tiedot sessioon >> käytetään työtilauksessa (voisi käyttää muokkaa sivullakin, muokkaa jos jää aikaa)
						$kirjautuneen_tiedot = array($avain,$nimi_f,$tunnus,$puh_f,$email_f,$k_osoite_f,$l_osoite_f,$as_avain_f);
						$_SESSION["kirjautuneen_tiedot"] = $kirjautuneen_tiedot;
						?><script> location.replace("etusivu.php"); </script><?php
					}
					else {									
						echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
					}
				}
			}
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