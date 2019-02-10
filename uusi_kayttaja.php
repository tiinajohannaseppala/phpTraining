<?php 
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Uusi Käyttäjä</title>
        <meta name="description" content="PHP-ohjelmointi">
        <meta name="author" content="Tiina Seppälä">
		<style>
			td, th {text-align: left; padding: 4px;}
		</style>
    </head>
    <body>
		<h2>Luo uusi käyttäjä</h2>		
        <form method="POST" action="">
			<h4>Pakolliset tiedot:</h4>
			<table>
			<tr><td>Nimi:				</td><td><input type="text" name="nimi" value="<?php if (isset($_POST["nimi"])) echo $_POST["nimi"]; ?>"></td></tr>
			<tr><td>Puhelinnumero:		</td><td><input type="text" name="puh" value="<?php if (isset($_POST["puh"])) echo $_POST["puh"]; ?>"></td></tr>
			<tr><td>Sähköpostiosoite:	</td><td><input type="text" name="email" value="<?php if (isset($_POST["email"])) echo $_POST["email"]; ?>"></td></tr>
			
			<tr><td>Käyntiosoite:		</td><td><input type="text" name="k_osoite" value="<?php if (isset($_POST["k_osoite"])) echo $_POST["k_osoite"]; ?>"></td></tr>
			<tr><td>Laskutusosoite:		</td><td><input type="text" name="l_osoite" value="<?php if (isset($_POST["l_osoite"])) echo $_POST["l_osoite"]; ?>"></td></tr>
			
			<tr><td>Käyttäjätunnus:		</td><td><input type="text" name="tunnus" value="<?php if (isset($_POST["tunnus"])) echo $_POST["tunnus"]; ?>"></td></tr>
			<tr><td>Salasana:			</td><td><input type="text" name="salasana1"></td></tr>
			<tr><td>Varmista salasana:	</td><td><input type="text" name="salasana2"></td></tr>
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
			else {
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
			<br>
            <input type="submit" name="tallenna" value="Tallenna">
			<input type="submit" name="peruuta" value="Peruuta">
        </form>
		<br>
<?php
//PERUUTA-nappia painettiin > vie takaisin etusivulle
		if (isset($_POST["peruuta"])) {
			?><script> location.replace("kirjaudu.php"); </script><?php
		}
//TALLENNA-nappia painettiin
// Tallennetaan saadut tiedot uusiin muuttujiin 
		if (isset($_POST["tallenna"])) {
			$nimi = $_POST["nimi"];
			$tunnus= $_POST["tunnus"];
			$salasana1 = $_POST["salasana1"];
			$salasana2 = $_POST["salasana2"];
			$puh = $_POST["puh"];
			$email = $_POST["email"];
			$k_osoite = $_POST["k_osoite"];
			$l_osoite = $_POST["l_osoite"];
			$paivays = date("Y-m-d");
			$as_avain = $_POST["asunto"];
//Tarkistaa että kaikki tiedot on syötetty
			if (strlen($nimi) < 1 or strlen($tunnus) < 1 or strlen($salasana1) < 1 or strlen($salasana2) < 1 or strlen($puh) < 1 or strlen($email) < 1 or strlen($k_osoite) < 1 or strlen($l_osoite) < 1) {
				echo "<b>Uuden käyttäjän luominen epäonnistui.</b> Tarkista, että kaikki pyydetyt tiedot on syötetty.<br>";
			}
//Tarkistaa, että salasana syötettiin oikein molempiin ruutuihin
			if ($salasana1 != $salasana2) {
				echo "<b>Virhe salasanaa luotaessa.</b> Tarkista, että antamasi salasana on syötetty oikein molempiin kenttiin.<br>";
			}
// OK kaikki tiedot syöytetty
			if ((strlen($nimi) >= 1 AND strlen($tunnus)) >= 1 AND strlen($salasana1) >= 1 AND strlen($salasana2) >= 1 AND $salasana1 == $salasana2 AND strlen($puh) >= 1 AND strlen($email) >= 1 AND strlen($k_osoite) >= 1 AND strlen($l_osoite) >= 1) {			
// ottaa yhteyden tietokantaan ja jos löytää tunnuksen sieltä, tulostaa virheilmoituksen
				$conn = mysqli_connect("localhost","root", "", "KAYTTAJA"); 
				$haku = "SELECT avain, tunnus FROM asiakas WHERE tunnus='$tunnus'";
				$vastaus = mysqli_query($conn,$haku);
				if (!$vastaus) {
					echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
					mysqli_close($conn);
				}
				elseif (mysqli_num_rows($vastaus) != 0) { 
					echo "<b>Valitsemasi käyttäjätunnus on varattu.</b> Valitse uusi käyttäjätunnus.";
					mysqli_close($conn);
				}
//Yrittää tallentaa ASIAKAS-tauluun annetut tiedot
				else {
//Jos asuntoa ei valittu, as_avain on NULL (as_avainta ei lisätä)
					if ($as_avain == 0) {
						$conn = mysqli_connect("localhost","root", "", "KAYTTAJA"); 
						$lisays = "INSERT INTO asiakas (nimi, tunnus, salasana, puh, email, kayntios, laskutusos, luontipvm) VALUES ('$nimi', '$tunnus', '$salasana1', '$puh', '$email', '$k_osoite', '$l_osoite', '$paivays')"; 
						$vastaus = mysqli_query($conn,$lisays); // suoritetaan kysely
						if ($vastaus) {
							mysqli_close($conn);
							$uusi_kayttaja = "Uuden käyttäjän luominen onnistui! Voit nyt kirjautua järjestelmään.";
							$_SESSION["uusi_kayttaja"] = $uusi_kayttaja;
							?><script> location.replace("kirjaudu.php"); </script><?php
						}
						else {									
							echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
							mysqli_close($conn);
						}
					}
// muutoin edetään tämän kaavan mukaan (as_avain lisätään myös)
					else {
						$conn = mysqli_connect("localhost","root", "", "KAYTTAJA"); 
						$lisays = "INSERT INTO asiakas (nimi, tunnus, salasana, puh, email, kayntios, laskutusos, luontipvm, as_avain) VALUES ('$nimi', '$tunnus', '$salasana1', '$puh', '$email', '$k_osoite', '$l_osoite', '$paivays', '$as_avain')"; 
						$vastaus = mysqli_query($conn,$lisays); // suoritetaan kysely
						if ($vastaus) {
							mysqli_close($conn);
							$uusi_kayttaja = "Uuden käyttäjän luominen onnistui! Voit nyt kirjautua järjestelmään.";
							$_SESSION["uusi_kayttaja"] = $uusi_kayttaja;
							?><script> location.replace("kirjaudu.php"); </script><?php
						}
						else {									
							echo "Jokin meni pieleen (<i>" . mysqli_error($conn) . "</i>)!";
							mysqli_close($conn);
						}
					}
				}
			}
		}
?>
	</body> 
</html>