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
			if(isset($_SESSION['kirjautuneen_tiedot'][7])) { $as_avain_kayttaja = $_SESSION['kirjautuneen_tiedot'][7];}
		}
// Muunnetaan Rakennustyyppinumero tekstiksi (tietojen tallentaminen)
			function rakennustyyppi ($rakennusteksti,$conn) {
				$conn = mysqli_connect("localhost","root", "", "KAYTTAJA"); 
				$haku = "SELECT avain, selite FROM asuntotyyppi WHERE avain='$rakennusteksti'"; 
				$vastaus = mysqli_query($conn, $haku); 
				while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) { 
					$selite = $rivi["selite"];
					return $selite;
				}
				mysqli_close($conn);
			}
// Tuodaan tieto tilauksen avaimesta linkin ja getin avulla
		if (isset($_GET['avain'])) { 
			$avain_tilaus = $_GET["avain"]; 
			$tilaaja_tilaus = $_GET["tilaaja"];
			$laskutusosoite_tilaus = $_GET["laskutusosoite"]; 
			$kuvaus_tilaus = $_GET["kuvaus"];
		}

// Muunnetaan Rakennustyyppi numeroksi
			function rakennusnum ($rakennusnum,$conn) {
			$conn = mysqli_connect("localhost","root", "", "KAYTTAJA"); 
				$haku = "SELECT avain, selite FROM asuntotyyppi WHERE selite='$rakennusnum'"; 
				$vastaus = mysqli_query($conn, $haku); 
				while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) { 
					$avain = $rivi["avain"];
					return $avain;
				}
				mysqli_close($conn);
			}
// Vapaaehtoiset tiedot
			$con = mysqli_connect("localhost","root", "", "FIRMA"); 
			$haku = "SELECT rak_tyyppi, rak_pinta_ala, tont_pinta_ala FROM tilaukset WHERE avain='$avain_tilaus'"; 
				$vastaus = mysqli_query($con,$haku);
				if ($vastaus) {
					while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) { 
						$rak_tyyppi_tilaus = $rivi["rak_tyyppi"];
						$rak_pinta_ala_tilaus = $rivi["rak_pinta_ala"];
						$tont_pinta_ala_tilaus = $rivi["tont_pinta_ala"];
					}
					mysqli_close($con);
				}
?>
	<h2>Muokkaa tilausta</h2>		
	<form method="post" action="">
		<h4>Pakolliset tiedot:</h4>
		<table>
			<tr><td>Päivitetty tilauspäivä:	</td><td><?php $tanaan = date("Y/m/d"); echo $tanaan;?></td></tr>
			<tr><td>Tilaaja:				</td><td><?php echo $tilaaja_tilaus;?></td></tr>
			<tr><td>Laskutusosoite:			</td><td><input type="text" name="l_osoite" value="<?php if (isset($_POST["l_osoite"])) {echo $_POST["l_osoite"];} else {echo $laskutusosoite_tilaus;} ?>"></td></tr>
			<tr><td>						</td><td style="color: grey;"><i>HUOM! Laskutusosoitteen muuttaminen ei päivity käyttäjätietoihin</i></td></tr>
			<tr><td>Kuvaus työstä:			</td><td><input type="text" name="kuvaus" value="<?php if (isset($_POST["kuvaus"])) {echo $_POST["kuvaus"];} else {echo $kuvaus_tilaus;} ?>"></td></tr>
		</table>
		<h4>Vapaaehtoiset tiedot:</h4>
		<table>
			<tr><td>Rakennuksen tyyppi:				</td><td><select name="asunto">
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
		if (isset($rak_tyyppi_tilaus)) {
			$rak_tyyppi_tilaus = rakennusnum($rak_tyyppi_tilaus,$conn);
			if ($rak_tyyppi_tilaus >= 1 AND $rak_tyyppi_tilaus <= 8) {
				while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) {
					$avain = $rivi["avain"];
					$selite = $rivi["selite"];
					if ($avain != $rak_tyyppi_tilaus) {
						echo "<option value='$avain'>$selite</option>";
					}
					if ($avain == $rak_tyyppi_tilaus){
						echo "<option value='$avain' selected='selected'>$selite</option>";
					}
				}
				echo "<option value='0'>poista valinta</option>";
			}
		//mysqli_close($conn);
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
			<tr><td></td><td style="color: grey;"><i>HUOM! Rakennustyypin muuttaminen ei tallennu käyttäjän tietoihin vaan ainoastaan tilaukseen.</i></td></tr>
			<tr><td>Rakennuksen pinta-ala (m2):		</td><td><input type="text" name="ala" value="<?php if(isset($rak_pinta_ala)) {echo $rak_pinta_ala;} else {echo $rak_pinta_ala_tilaus;}?>"></td></tr>
			<tr><td>Tontin pinta-ala (m2):			</td><td><input type="text" name="tontti" value="<?php if(isset($tont_pinta_ala)) {echo $tont_pinta_ala;} else {echo $tont_pinta_ala_tilaus;}?>"></td></tr>
		</table>
		<br>
		<input type="submit" name="tallenna_muutokset" value="Tallenna muutokset">
		<input type="submit" name="peruuta" value="Peruuta">
	</form>
	<br>
<?php

//PERUUTA-nappia painettiin > vie takaisin etusivulle
	if (isset($_POST["peruuta"])) {
		?><script>location.replace("etusivu.php");</script><?php
	}
//TALLENNA-nappia painettiin - tallennetaan saadut tiedot uusiin muuttujiin 
// HUOM! STATUS-AVAIN, koska kiinteistöfirman järjestelmää ei ole luotu, päivittämällä tilausta, tilaus päivittyy aloitetuksi
	if (isset($_POST["tallenna_muutokset"])) {
		$status_avain = 2; // ALOITETTU
		$laskutusosoite = $_POST["l_osoite"];
		$kuvaus = $_POST["kuvaus"];
		$rak_tyyppi = rakennustyyppi($_POST["asunto"],$conn); // Tekstiä, esim. maatila
		$rak_pinta_ala = intval($_POST["ala"]);
		$tont_pinta_ala = intval($_POST["tontti"]);
//lisäksi lähetetään FIRMALLE muita tarvittavia tietoja (normi laajuus tehtävästä)
		$tilauspvm = date("Y-m-d");
		$aloituspvm = date("Y-m-d");
		$valmistumispvm = "2099-12-31";
		$hyvaksymispvm = date("Y-m-d");
		$kustannusarvio = 1500;
		$kommentti = "Tyo aloitetaan samantien";
		$kaytetyt_tunnit = 4;
		$kuluneet_tarvikkeet = "Ei voida viela listata";
//Tarkistaa että kuvaus on syötetty
		if (strlen($kuvaus) < 1) {
			echo "<b>Tilauksen päivittäminen epäonnistui.</b> Syötä kuvaus tarvitsemastasi työstä.<br>";
		}
// OK kuvaus syötetty
		if ((strlen($kuvaus) >= 1)) {
			$con = mysqli_connect("localhost","root", "", "FIRMA"); 
// Jos MITÄÄN vapaaehtoisia tietoja ei ole syötetty
			if ($rak_tyyppi == 0 AND strlen($rak_pinta_ala) < 1 AND $tont_pinta_ala < 1) {
				$muokkaus = "UPDATE tilaukset SET laskutusosoite='$laskutusosoite',kuvaus='$kuvaus_tilaus',tilauspvm='$tilauspvm',aloituspvm='$aloituspvm',valmistumispvm='$valmistumispvm',hyvaksymispvm='$hyvaksymispvm',kommentti='$kommentti',kuluneet_tarvikkeet='$kuluneet_tarvikkeet',kaytetyt_tunnit='$kaytetyt_tunnit',kustannusarvio='$kustannusarvio',status_avain='$status_avain' WHERE avain='$avain_tilaus'"; 
				$vastaus = mysqli_query($con,$muokkaus);
				if ($vastaus) {
					mysqli_close($con);
					$muokkaus_tilaus = "Tilauksen päivitys onnistui.";
					$_SESSION["muokkaus_tilaus"] = $muokkaus_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}
// Jos Rakennustyyppi ja rakennuspinta-ala on syötetty
			if ($rak_tyyppi != 0 AND strlen($rak_pinta_ala) >= 1 AND $tont_pinta_ala < 1) {
				$muokkaus = "UPDATE tilaukset SET laskutusosoite='$laskutusosoite',kuvaus='$kuvaus_tilaus',rak_tyyppi='$rak_tyyppi',rak_pinta_ala='$rak_pinta_ala',tilauspvm='$tilauspvm',aloituspvm='$aloituspvm',valmistumispvm='$valmistumispvm',hyvaksymispvm='$hyvaksymispvm',kommentti='$kommentti',kuluneet_tarvikkeet='$kuluneet_tarvikkeet',kaytetyt_tunnit='$kaytetyt_tunnit',kustannusarvio='$kustannusarvio',status_avain='$status_avain' WHERE avain='$avain_tilaus'"; 
				$vastaus = mysqli_query($con,$muokkaus);
				if ($vastaus) {
					mysqli_close($con);
					$muokkaus_tilaus = "Tilauksen päivitys onnistui.";
					$_SESSION["muokkaus_tilaus"] = $muokkaus_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}
// Jos Rakennustyyppi ja tonttipinta-ala on syötetty
			elseif ($rak_tyyppi != 0 AND strlen($rak_pinta_ala) > 1 AND $tont_pinta_ala >= 1) {
				$muokkaus = "UPDATE tilaukset SET laskutusosoite='$laskutusosoite',kuvaus='$kuvaus_tilaus',rak_tyyppi='$rak_tyyppi',tont_pinta_ala='$tont_pinta_ala',tilauspvm='$tilauspvm',aloituspvm='$aloituspvm',valmistumispvm='$valmistumispvm',hyvaksymispvm='$hyvaksymispvm',kommentti='$kommentti',kuluneet_tarvikkeet='$kuluneet_tarvikkeet',kaytetyt_tunnit='$kaytetyt_tunnit',kustannusarvio='$kustannusarvio',status_avain='$status_avain' WHERE avain='$avain_tilaus'"; 
				$vastaus = mysqli_query($con,$muokkaus);
				if ($vastaus) {
					mysqli_close($con);
					$muokkaus_tilaus = "Tilauksen päivitys onnistui.";
					$_SESSION["muokkaus_tilaus"] = $muokkaus_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}
// Jos Rakennuspinta-ala ja tonttipinta-ala on syötetty
			elseif ($rak_tyyppi == 0 AND strlen($rak_pinta_ala) >= 1 AND $tont_pinta_ala >= 1) {
				$muokkaus = "UPDATE tilaukset SET laskutusosoite='$laskutusosoite',kuvaus='$kuvaus_tilaus',rak_pinta_ala='$rak_pinta_ala',tont_pinta_ala='$tont_pinta_ala',tilauspvm='$tilauspvm',aloituspvm='$aloituspvm',valmistumispvm='$valmistumispvm',hyvaksymispvm='$hyvaksymispvm',kommentti='$kommentti',kuluneet_tarvikkeet='$kuluneet_tarvikkeet',kaytetyt_tunnit='$kaytetyt_tunnit',kustannusarvio='$kustannusarvio',status_avain='$status_avain' WHERE avain='$avain_tilaus'"; 
				$vastaus = mysqli_query($con,$muokkaus);
				if ($vastaus) {
					mysqli_close($con);
					$muokkaus_tilaus = "Tilauksen päivitys onnistui.";
					$_SESSION["muokkaus_tilaus"] = $muokkaus_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}
// Jos KAIKKI vapaaehtoiset on syötetty
			else {	
				$muokkaus = "UPDATE tilaukset SET laskutusosoite='$laskutusosoite',kuvaus='$kuvaus_tilaus',rak_tyyppi='$rak_tyyppi',rak_pinta_ala='$rak_pinta_ala',tont_pinta_ala='$tont_pinta_ala',tilauspvm='$tilauspvm',aloituspvm='$aloituspvm',valmistumispvm='$valmistumispvm',hyvaksymispvm='$hyvaksymispvm',kommentti='$kommentti',kuluneet_tarvikkeet='$kuluneet_tarvikkeet',kaytetyt_tunnit='$kaytetyt_tunnit',kustannusarvio='$kustannusarvio',status_avain='$status_avain' WHERE avain='$avain_tilaus'"; 
				$vastaus = mysqli_query($con,$muokkaus);
				if ($vastaus) {
					mysqli_close($con);
					$muokkaus_tilaus = "Tilauksen päivitys onnistui.";
					$_SESSION["muokkaus_tilaus"] = $muokkaus_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {	
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}

		}
	}
?>
<!-- ULOSKIRJAUTUMINEN vie omalle sivulle kirjaudu_ulos.php-->
	<br><br><br><br>
	<form action="kirjaudu_ulos.php" method="POST"><input type="submit" name="ulos" value="Kirjaudu ulos"</input></form>
	
<!-- if(isset($_SESSION["kirjautunut"])) lopetus -->
<?php
		}
			else { 
				echo "Olet kirjautunut ulos järjestelmästä. Sinun tulee <a href='kirjaudu.php'>kirjautua sisään</a> nähdäksesi sivun.";
			}
?>
</body> 
</html>