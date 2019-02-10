<?php 
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Uusi työtilaus</title>
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
			
// Tuodaan käyttäjän tiedot arrayssä sivulle ja tallennetaan muuttujiin
		if (isset($_SESSION['kirjautuneen_tiedot'])) {
			$nimi_kayttaja = $_SESSION['kirjautuneen_tiedot'][1];
			$laskutusos_kayttaja = $_SESSION['kirjautuneen_tiedot'][6];
			$as_avain_kayttaja = $_SESSION['kirjautuneen_tiedot'][7]; //numero
		}	
// Muunnetaan Rakennustyyppinumero tekstiksi (tietojen tallentaminen)
			function rakennustyyppi ($rakennusnum,$conn) {
				$conn = mysqli_connect("localhost","root", "", "KAYTTAJA"); 
				$haku = "SELECT avain, selite FROM asuntotyyppi WHERE avain='$rakennusnum'"; 
				$vastaus = mysqli_query($conn, $haku); 
				while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) { 
					$selite = $rivi["selite"];
					return $selite;
				}
				mysqli_close($conn);
			}
?>
	<h2>Uusi tilaus</h2>		
	<form method="post" action="">
		<h4>Pakolliset tiedot:</h4>
		<table>
			<tr><td>Tilauspäivä:		</td><td><?php $aika = date("d/m/Y"); echo $aika;?></td></tr>
			<tr><td>Tilaaja:			</td><td><?php if (isset($nimi_kayttaja)) {echo $nimi_kayttaja;}?></td></tr>
			<tr><td>Laskutusosoite:			</td><td><input type="text" name="l_osoite" value="<?php if (isset($_POST["l_osoite"])) {echo $_POST["l_osoite"];} else {echo $laskutusos_kayttaja;} ?>"></td></tr>
			<tr><td>						</td><td style="color: grey;"><i>HUOM! Laskutusosoitteen muuttaminen ei päivity käyttäjätietoihin vaan ainoastaan tilaukseen.</i></td></tr>
			<tr><td>Kuvaus työstä:		</td><td><input type="text" name="kuvaus" value="<?php if (isset($_POST["kuvaus"])) echo $_POST["kuvaus"]; ?>"></td></tr>
		</table>
		<h4>Vapaaehtoiset tiedot:</h4>
		<table>
			<tr><td>Rakennuksen tyyppi:</td><td><select name="asunto"><option value="0" selected="selected">Valitse</option>
<?php
// hakee tietokannasta kaikki asuntotyypit ja tulostaa vaihtoehdoiksi dropdown-valikkoon
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
					elseif ($as_avain_kayttaja >= 1 AND $as_avain_kayttaja <= 8) {
						while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) {
							$avain = $rivi["avain"];
							$selite = $rivi["selite"];
							if ($avain != $as_avain_kayttaja) {
								echo "<option value='$avain'>$selite</option>";
							}
							if ($avain == $as_avain_kayttaja){
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
			<tr><td></td><td style="color: grey;"><i>HUOM! Rakennustyypin muuttaminen ei tallennu käyttäjän tietoihin vaan ainoastaan tilaukseen.</i></td></tr>
			
			<tr><td>Rakennuksen pinta-ala (m2):		</td><td><input type="text" name="ala" value="<?php if (isset($_POST["ala"])) {echo $_POST["ala"];}?>"></td></tr>
			<tr><td>Tontin pinta-ala (m2):			</td><td><input type="text" name="tontti" value="<?php if (isset($_POST["tontti"])) {echo $_POST["tontti"];}?>"></td></tr>
		</table>
		<br>
		<input type="submit" name="tallenna_tilaus" value="Tallenna tilaus">
		<input type="submit" name="peruuta" value="Peruuta">
	</form>
	<br>
<?php
//PERUUTA-nappia painettiin > vie takaisin etusivulle
	if (isset($_POST["peruuta"])) {
		?><script>location.replace("etusivu.php");</script><?php
	}
//TALLENNA-nappia painettiin
// Tallennetaan saadut tiedot uusiin muuttujiin 
	if (isset($_POST["tallenna_tilaus"])) {
		$status_avain = 1; // TILATTU
		$kuvaus = $_POST["kuvaus"];
		$rak_tyyppi = rakennustyyppi($_POST["asunto"],$conn); // Tekstiä, esim. maatila
		$rak_pinta_ala = intval($_POST["ala"]);
		$tont_pinta_ala = intval($_POST["tontti"]);
//lisäksi lähetetään FIRMALLE muita tarvittavia tietoja (normi laajuus tehtävästä)
		$tilauspvm = date("Y-m-d");
		$aloituspvm = "2099-12-31";
		$valmistumispvm = "2099-12-31";
		$hyvaksymispvm = "2099-12-31";
		$kustannusarvio = 0;
		$kommentti = "Tarvitaan tarkentavia tietoja kustannusarvion antamiseen";
		$kaytetyt_tunnit = 0;
		$kuluneet_tarvikkeet = "-";
//Tarkistaa että kuvaus on syötetty
		if (strlen($kuvaus) < 1) {
			echo "<b>Uuden tilauksen luominen epäonnistui.</b> Syötä kuvaus tarvitsemastasi työstä.<br>";
		}
// OK kuvaus syötetty
		if ((strlen($kuvaus) >= 1)) {
			$con = mysqli_connect("localhost","root", "", "FIRMA"); 
// Jos MITÄÄN vapaaehtoisia tietoja ei ole syötetty
			if ($rak_tyyppi == 0 AND strlen($rak_pinta_ala) < 1 AND $tont_pinta_ala < 1) {
				$lisays = "INSERT INTO tilaukset (tilaaja,laskutusosoite,kuvaus,tilauspvm,aloituspvm,valmistumispvm,hyvaksymispvm,kommentti,kaytetyt_tunnit,kuluneet_tarvikkeet,kustannusarvio,status_avain) VALUES ('$nimi_kayttaja','$laskutusos_kayttaja','$kuvaus','$tilauspvm','$aloituspvm','$valmistumispvm','$hyvaksymispvm','$kommentti','$kaytetyt_tunnit','$kuluneet_tarvikkeet','$kustannusarvio','$status_avain')"; 
				$vastaus = mysqli_query($con,$lisays);
				if ($vastaus) {
					mysqli_close($con);
					$uusi_tilaus = "Uuden tilauksen luominen onnistui.";
					$_SESSION["uusi_tilaus"] = $uusi_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}
// Jos Rakennustyyppi ja rakennuspinta-ala on syötetty
			if ($rak_tyyppi != 0 AND strlen($rak_pinta_ala) >= 1 AND $tont_pinta_ala < 1) {
				//$con = mysqli_connect("localhost","root", "", "FIRMA"); 
				$lisays = "INSERT INTO tilaukset (tilaaja,laskutusosoite,kuvaus,rak_tyyppi,rak_pinta_ala,tilauspvm,aloituspvm,valmistumispvm,hyvaksymispvm,kommentti,kaytetyt_tunnit,kuluneet_tarvikkeet,kustannusarvio,status_avain) VALUES ('$nimi_kayttaja','$laskutusos_kayttaja','$kuvaus','$rak_tyyppi','$rak_pinta_ala','$tilauspvm','$aloituspvm','$valmistumispvm','$hyvaksymispvm','$kommentti','$kaytetyt_tunnit','$kuluneet_tarvikkeet','$kustannusarvio','$status_avain')"; 
				$vastaus = mysqli_query($con,$lisays);
				if ($vastaus) {
					mysqli_close($con);
					$uusi_tilaus = "Uuden tilauksen luominen onnistui.";
					$_SESSION["uusi_tilaus"] = $uusi_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}
// Jos Rakennustyyppi ja tonttipinta-ala on syötetty
			elseif ($rak_tyyppi != 0 AND strlen($rak_pinta_ala) > 1 AND $tont_pinta_ala >= 1) {
				//$con = mysqli_connect("localhost","root", "", "FIRMA"); 
				$lisays = "INSERT INTO tilaukset (tilaaja,laskutusosoite,kuvaus,rak_tyyppi,tont_pinta_ala,tilauspvm,aloituspvm,valmistumispvm,hyvaksymispvm,kommentti,kaytetyt_tunnit,kuluneet_tarvikkeet,kustannusarvio,status_avain) VALUES ('$nimi_kayttaja','$laskutusos_kayttaja','$kuvaus','$rak_tyyppi','$tont_pinta_ala','$tilauspvm','$aloituspvm','$valmistumispvm','$hyvaksymispvm','$kommentti','$kaytetyt_tunnit','$kuluneet_tarvikkeet','$kustannusarvio','$status_avain')"; 
				$vastaus = mysqli_query($con,$lisays); 
				if ($vastaus) {
					mysqli_close($con);
					$uusi_tilaus = "Uuden tilauksen luominen onnistui.";
					$_SESSION["uusi_tilaus"] = $uusi_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}
// Jos Rakennuspinta-ala ja tonttipinta-ala on syötetty
			elseif ($rak_tyyppi == 0 AND strlen($rak_pinta_ala) >= 1 AND $tont_pinta_ala >= 1) {
				//$con = mysqli_connect("localhost","root", "", "FIRMA"); 
				$lisays = "INSERT INTO tilaukset (tilaaja,laskutusosoite,kuvaus,rak_pinta_ala,tont_pinta_ala,tilauspvm,aloituspvm,valmistumispvm,hyvaksymispvm,kommentti,kaytetyt_tunnit,kuluneet_tarvikkeet,kustannusarvio,status_avain) VALUES ('$nimi_kayttaja','$laskutusos_kayttaja','$kuvaus','$rak_pinta_ala','$tont_pinta_ala','$tilauspvm','$aloituspvm','$valmistumispvm','$hyvaksymispvm','$kommentti','$kaytetyt_tunnit','$kuluneet_tarvikkeet','$kustannusarvio','$status_avain')"; 
				$vastaus = mysqli_query($con,$lisays); // suoritetaan kysely
				if ($vastaus) {
					mysqli_close($con);
					$uusi_tilaus = "Uuden tilauksen luominen onnistui.";
					$_SESSION["uusi_tilaus"] = $uusi_tilaus;
					?><script> location.replace("etusivu.php"); </script><?php
				}
				else {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
			}
// Jos KAIKKI vapaaehtoiset on syötetty
			else {	//($rak_tyyppi != 0 AND strlen($rak_pinta_ala) >= 1 AND $tont_pinta_ala >= 1)		
				//$con = mysqli_connect("localhost","root", "", "FIRMA"); 
				$lisays = "INSERT INTO tilaukset (tilaaja,laskutusosoite,kuvaus,rak_tyyppi,rak_pinta_ala,tont_pinta_ala,tilauspvm,aloituspvm,valmistumispvm,hyvaksymispvm,kommentti,kaytetyt_tunnit,kuluneet_tarvikkeet,kustannusarvio,status_avain) VALUES ('$nimi_kayttaja','$laskutusos_kayttaja','$kuvaus','$rak_tyyppi','$rak_pinta_ala','$tont_pinta_ala','$tilauspvm','$aloituspvm','$valmistumispvm','$hyvaksymispvm','$kommentti','$kaytetyt_tunnit','$kuluneet_tarvikkeet','$kustannusarvio','$status_avain')"; 
				$vastaus = mysqli_query($con,$lisays); // suoritetaan kysely
				if ($vastaus) {
					mysqli_close($con);
					$uusi_tilaus = "Uuden tilauksen luominen onnistui.";
					$_SESSION["uusi_tilaus"] = $uusi_tilaus;
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