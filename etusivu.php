<?php 
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Omat työtilaukset</title>
        <meta name="description" content="PHP-ohjelmointi">
        <meta name="author" content="Tiina Seppälä">
		<style>
			table {border-collapse: collapse;}
			table, th, td {border: 1px solid black;}
			td {width: 150px; text-align: left; padding: 4px;}
		</style>
    </head>
    <body>
<?php
// kaikki sivun tiedot näkyvät vain kirjautuneelle, joten kaiken sisällön tulee olla if(isset($_SESSION["kirjautunut"])) sisällä
	if(isset($_SESSION["kirjautunut"])) {
		echo "Kirjautunut: " . $_SESSION["kirjautunut"] . "<br>";	
?>
		<h2>Omat työtilaukset</h2>
<?php
// Käyttäjätietojen päivitys onnistui
			if (isset($_SESSION['tietojen_paivitys1'])) {
				echo $_SESSION['tietojen_paivitys1']; 
				unset($_SESSION['tietojen_paivitys1']);
			}
// Käyttäjätietojen ja SALASANAN päivitys onnistui
			if (isset($_SESSION['tietojen_paivitys2'])) {
				echo $_SESSION['tietojen_paivitys2']; 
				unset($_SESSION['tietojen_paivitys2']);
			}	
// Tuodaan käyttäjän tiedot arrayssä sivulle ja tallennetaan muuttujiin
			if (isset($_SESSION['kirjautuneen_tiedot'])) { 
				$nimi_kayttaja = $_SESSION['kirjautuneen_tiedot'][1];
			}
// Uuden tilauksen luominen onnistui
			if (isset($_SESSION['uusi_tilaus'])) {
				echo $_SESSION['uusi_tilaus']; 
				unset($_SESSION['uusi_tilaus']);
			}
// Tilauksen poisto onnistui
			if (isset($_SESSION['tilauksen_poisto'])) {
				echo $_SESSION['tilauksen_poisto']; 
				unset($_SESSION['tilauksen_poisto']);
			}		
// Haetaan status ja palautetaan taulukkoon
			function status($status_avain_tilaus,$con){
				$con = mysqli_connect("localhost","root", "", "FIRMA"); 
				$haku_asty = "SELECT avain, tila FROM status WHERE avain = '$status_avain_tilaus'";
				$vastaus_asty = mysqli_query($con, $haku_asty);
				$rivi_asty = mysqli_fetch_array($vastaus_asty, MYSQL_ASSOC);
				$tila = $rivi_asty["tila"];
				return $tila;
				mysqli_close($con);
			}	
// Haetaan taulukkoon tiedot FIRMA-tietokannasta
				$con = mysqli_connect("localhost","root", "", "FIRMA"); 
				$haku = "SELECT avain, tilaaja, laskutusosoite, kuvaus, rak_tyyppi, rak_pinta_ala, tont_pinta_ala, tilauspvm, aloituspvm, valmistumispvm, hyvaksymispvm, kommentti, kustannusarvio, status_avain FROM tilaukset WHERE tilaaja='$nimi_kayttaja'";
				$vastaus = mysqli_query($con,$haku);
				if (!$vastaus) {
					echo "Jokin meni pieleen (<i>" . mysqli_error($con) . "</i>)!";
					mysqli_close($con);
				}
// Jos ei työtilauksia ilmoitetaan siitä
				elseif (mysqli_num_rows($vastaus) == 0) { 
					echo "<b>Ei tehtyjä työtilauksia.</b>";
					mysqli_close($con);
				}
// jos tilauksia löytyy, tulostetaan ne taulukkoon
				else {
					echo "<table><tr>
					<th><b>Työn tila</b></th>
					<th><b>Työn kuvaus</b></th>
					<th><b>Tilauspvm</b></th>
					<th><b>Aloituspvm</b></th>
					<th><b>Valmistumispvm</b></th>
					<th><b>Hyväksymispvm</b></th>
					<th><b>Toimittajan kommentti</b></th>
					<th><b>Kustannusarvio(€)</b></th>
					<th><b>Toiminnot</b></th>
					</tr>";
					while ($rivi = mysqli_fetch_array($vastaus, MYSQL_ASSOC)) {
//tiedot tallennettu muuttujiin
						$avain_tilaus = $rivi["avain"]; 
						$tilaaja_tilaus = $rivi["tilaaja"];
						$laskutusosoite_tilaus = $rivi["laskutusosoite"]; 
						$kuvaus_tilaus = $rivi["kuvaus"];
						$tilauspvm_tilaus = $rivi["tilauspvm"];
								$tilauspvm_tilaus_suomi = date("d-m-Y", strtotime($tilauspvm_tilaus));
								
						$aloituspvm_tilaus = $rivi["aloituspvm"]; 
								
								$aloituspvm_tilaus_suomi = implode('-', array_reverse(explode('-', $aloituspvm_tilaus))); 
								if ($aloituspvm_tilaus_suomi == "31-12-2099") {$aloituspvm_tilaus_suomi = "Ei vielä arvioitu";}
								
						$valmistumispvm_tilaus = $rivi["valmistumispvm"]; 
								$valmistumispvm_tilaus_suomi = implode('-', array_reverse(explode('-', $valmistumispvm_tilaus)));; 
								if ($valmistumispvm_tilaus_suomi == "31-12-2099") {$valmistumispvm_tilaus_suomi = "Ei vielä arvioitu";}
								
						$hyvaksymispvm_tilaus = $rivi["hyvaksymispvm"]; 
								$hyvaksymispvm_tilaus_suomi = implode('-', array_reverse(explode('-', $hyvaksymispvm_tilaus))); 
								if ($hyvaksymispvm_tilaus_suomi == "31-12-2099") {$hyvaksymispvm_tilaus_suomi = "Ei vielä arvioitu";}
								
						$kommentti_tilaus = $rivi["kommentti"]; 
						$kustannusarvio_tilaus = $rivi["kustannusarvio"]; 
						$status_avain_tilaus = status($rivi["status_avain"],$con);
						echo "<tr>
						<td><b>$status_avain_tilaus</b></td>
						<td>$kuvaus_tilaus</td>
						<td>$tilauspvm_tilaus_suomi</td>
						<td>$aloituspvm_tilaus_suomi</td>
						<td>$valmistumispvm_tilaus_suomi</td>
						<td>$hyvaksymispvm_tilaus_suomi</td>
						<td>$kommentti_tilaus</td>
						<td>$kustannusarvio_tilaus</td>";
						if ($status_avain_tilaus == "tilattu") {
							echo "<td><a href='poista_tilaus.php?avain=$avain_tilaus'>Poista</a>  
							<a href='muokkaa_tilausta.php?avain=$avain_tilaus&tilaaja=$tilaaja_tilaus&laskutusosoite=$laskutusosoite_tilaus&kuvaus=$kuvaus_tilaus'>Muokkaa</a></td>";
						}
						else {
							echo "<td>Työ on jo aloitettu</td></tr>";
						}
					}
					mysqli_close($con);

				}	
?>
		</table>
		<br>
		<form action="uusi_tilaus.php" method="POST"><input type="submit" name="tilaa" value="Tee uusi tilaus"</input></form>
		<br>		
<!-- ULOSKIRJAUTUMINEN vie omalle sivulle kirjaudu_ulos.php-->
	<br><br><br><br>
	<form action="kirjaudu_ulos.php" method="POST"><input type="submit" name="ulos" value="Kirjaudu ulos"</input></form>
		
<!-- if(isset($_SESSION["kirjautunut"])) LOPETUS!!! -->
	<?php
	}
	else { 
		echo "Olet kirjautunut ulos järjestelmästä. Sinun tulee <a href='kirjaudu.php'>kirjautua sisään</a> nähdäksesi sivun.";
	}
	?>
	</body> 
</html>