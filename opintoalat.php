<!DOCTYPE html>
<html>
<head>
<style type="text/css">
</style>
<script>
</script>
<title><?php
set_time_limit ( 400 );
error_reporting ( 0 );
if (isset ( $_GET ['ala'] )) {
	echo $_GET ['ala'];
}
?></title>
<meta charset="UTF-8">

</head>
<body>
<?php
$kokotutkinto = array ();
if (isset ( $_GET ['kieli'] )) {
	$kieli = $_GET ['kieli'];
} else {
	$kieli = "fi";
}

?>


<?php

switch ($_GET ['lista']) {
	case rakenne :
		echo "<table border='1'>";
		rakenne ();
		break;
	case yhteinen :
		yhteinen ();
		break;
	case koulutuskoodi :
		koulutuskoodi ();
		break;
	case tutkinnonosat:
		if (isset($_GET['ala'])){
		echo "<table border='1'>";
		kaikkitutkinnonosat();
		echo "</table>";
		}else{
			echo "<form action='' method='get'>";
			echo "<input type='hidden' name='lista' value='tutkinnonosat'>";
			echo "<table border='1'>";
			alat_rakisi ();
			echo "</form>";
		}
		break;
	default :
		echo "<table border='1'>";
		alat ();
}
?>

<?php
function rakenne() {
	$ala = $_GET ['id'];
	$perusteet = json_decode ( file_get_contents ( 'https://eperusteet.opintopolku.fi/eperusteet-service/api/perusteet/' . $ala . '/kaikki' ), true );
	if ($perusteet ['suoritustavat'] ['0'] ['suoritustapakoodi'] == "ops") {
		$ops = 0;
	} else {
		$ops = 1;
	}
	$osat ['osat'] = $perusteet ['suoritustavat'] [$ops] ['rakenne'] ['osat'];
	$kierros = 0;
	$ylinkohta = 0;
	
	rakenne_osat ( $osat, $ops, $perusteet );
}
function rakenne_osat($osat, $ops, $perusteet) {
	// var_dump($osat);
	foreach ( $osat ['osat'] as $osa ) {
		if (isset ( $osa ['_tutkinnonOsaViite'] )) {
			$GLOBALS ['ylinkohta'] = $GLOBALS ['kierros'];
			echo "<tr>";
			for($a = 0; $a < $GLOBALS ['ylinkohta']; $a ++) {
				echo "</td><td>";
			}
			echo "<td></td>";
			tutkinnonOsaViitteet ( $ops, $perusteet, $osa ['_tutkinnonOsaViite'], $osa ['pakollinen'] );
		} else {
			echo "<tr>";
			if ($GLOBALS ['ylinkohta'] > 0) {
				for($a = 0; $a < $GLOBALS ['ylinkohta']; $a ++) {
					echo "</td><td>";
				}
			} else {
				for($a = 0; $a < $GLOBALS ['kierros']; $a ++) {
					echo "</td><td>";
				}
				$GLOBALS ['kierros'] = $GLOBALS ['kierros'] + 1;
			}
			echo "<td>" . $osa ['nimi'] [$GLOBALS ['kieli']] . "</td></tr>";
			rakenne_osat ( $osa, $ops, $perusteet );
		}
	}
}
function tutkinnonOsaViitteet($ops, $perusteet, $tutkinnonOsaViite, $pakollinen) {
	foreach ( $perusteet ['suoritustavat'] [$ops] ['tutkinnonOsaViitteet'] as $tutkinnonOsaViitteet ) {
		if ($tutkinnonOsaViitteet ['id'] == $tutkinnonOsaViite) {
			tutkinnonOsat ( $ops, $perusteet, $tutkinnonOsaViitteet ['_tutkinnonOsa'], $tutkinnonOsaViitteet ['laajuus'], $pakollinen );
		}
	}
}
function tutkinnonOsat($ops, $perusteet, $tutkinnonOsaViite, $laajuus, $pakollinen) {
	foreach ( $perusteet ['tutkinnonOsat'] as $tutkinnonOsa ) {
		if ($tutkinnonOsa ['id'] == $tutkinnonOsaViite) {
			echo "<td>" . $tutkinnonOsa ['nimi'] [$GLOBALS ['kieli']] . "</td>";
			echo "<td>" . $laajuus . "</td>";
			echo "<td>" . $tutkinnonOsa ['koodiArvo'] . "</td>";
			echo "<td>" . $tutkinnonOsa ['opintoluokitus'] . "</td>";
			if ($pakollinen == true) {
				echo "<td>pakollinen</td>";
			} else {
				echo "<td>valinnainen</td>";
			}
		}
	}
}
function alat() {
	$alat = json_decode ( file_get_contents ( 'https://virkailija.opintopolku.fi/eperusteet-service/api/perusteet?sivukoko=100&suoritustapa=ops' ), true );
	echo "<tr><td><H1>Alat</H1></td><td><a href='?lista=tulossakoulutuskoodi&yhteinen=636&rivi=&ala=koulutuskoodi'>koulutuskoodi</a><br><a href='?lista=tutkinnonosat'>tutkinnonosat</a><br><a href='?lista=tutkinnonosat&kieli=sv'>tutkinnonosat Ruotsiksi</a></td><tr>";
	foreach ( $alat ['data'] as $ala ) {
		echo "<tr><td><a href='?lista=rakenne&ala=";
		echo $ala ['nimi'] ['fi'];
		echo "&id=";
		echo $ala ['id'];
		echo "'>";
		echo $ala ['nimi'] ['fi'];
		echo "</a></td><td><a href='?lista=rakenne&ala=";
		echo $ala ['nimi'] ['sv'];
		echo "&id=";
		echo $ala ['id'];
		echo "&kieli=sv'>";
		echo $ala ['nimi'] ['sv'];
		echo "</a></td></tr>";
	}
	echo "<tr><td><H1>yhteiset tutkinnon osat</H1></td></tr>";
	echo "<tr><td><a href='?lista=yhteinen&yhteinen=636&rivi=&ala=Viestint&auml- ja vuorovaikutusosaaminen'>Viestint&auml- ja vuorovaikutusosaaminen</a></td><td><a href='?lista=yhteinen&yhteinen=636&kieli=sv&rivi=&ala=Kunnande i kommunikation och interaktion'>Kunnande i kommunikation och interaktion</a></td></tr>";
	echo "<tr><td><a href='?lista=yhteinen&yhteinen=637&rivi=&ala=Matemaattis-luonnontieteellinen osaaminen'>Matemaattis-luonnontieteellinen osaaminen</a></td><td><a href='?lista=yhteinen&yhteinen=637&kieli=sv&rivi=&ala=Kunnande i matematik och naturvetenskap'>Kunnande i matematik och naturvetenskap</a></td></tr>";
	echo "<tr><td><a href='?lista=yhteinen&yhteinen=638&rivi=&ala=Yhteiskunnassa ja ty&oumlel&aumlm&aumlss&auml tarvittava osaaminen'>Yhteiskunnassa ja ty&oumlel&aumlm&aumlss&auml tarvittava osaaminen</a></td><td><a href='?lista=yhteinen&yhteinen=638&kieli=sv&rivi=&Kunnande som beh&oumlvs i samh&aumlllet och i arbetslivet='>Kunnande som beh&oumlvs i samh&aumlllet och i arbetslivet</a></td></tr>";
	echo "<tr><td><a href='?lista=yhteinen&yhteinen=639&rivi=&ala=Sosiaalinen ja kulttuurinen osaaminen'>Sosiaalinen ja kulttuurinen osaaminen</a></td><td><a href='?lista=yhteinen&yhteinen=639&kieli=sv&rivi=&K&aumlnnedom om olika kulturer='>K&aumlnnedom om olika kulturer</a><br>";
}
function yhteinen() {
	$yhteiset = json_decode ( file_get_contents ( 'https://eperusteet.opintopolku.fi/eperusteet-service/api/perusteenosat/' . $_GET ['yhteinen'] . '/osaalueet' ), true );
	echo "<table border='1'><tr><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>";
	foreach ( $yhteiset as $oppiaine ) {
		echo "<tr><td>";
		echo $oppiaine ['nimi'] [$GLOBALS ['kieli']];
		echo "</td></tr>";
		// var_dump($oppiaine);
		foreach ( $oppiaine ['osaamistavoitteet'] as $oppianeen_osa ) {
			if (! empty ( $oppianeen_osa ['nimi'] [$GLOBALS ['kieli']] )) {
				echo "<tr><td></td><td>" . $oppianeen_osa ['nimi'] [$GLOBALS ['kieli']] . "</td>";
				echo "<td>" . $oppianeen_osa ['laajuus'] . "</td><td></td>";
				echo "<td>" . $oppianeen_osa ['pakollinen'] . "</td>";
				$etsi = array (
						"<li>",
						"</li>" 
				);
				$korvaa = array (
						"<td>",
						"</td>" 
				);
				
				if (isset ( $_GET ['rivi'] )) {
					$korvaa = array (
							"<tr><td></td><td></td><td></td><td></td><td>",
							"</td></tr>" 
					);
					$txt = str_replace ( $etsi, $korvaa, $oppianeen_osa ['tavoitteet'] [$GLOBALS ['kieli']] );
					echo "<td>" . $txt . "</td>";
				} else {
					$txt = str_replace ( $etsi, $korvaa, $oppianeen_osa ['tavoitteet'] [$GLOBALS ['kieli']] );
					echo "<td>" . $txt . "</td>";
				}
			}
		}
	}
}
function koulutuskoodi() {
	$alat = json_decode ( file_get_contents ( 'https://virkailija.opintopolku.fi/eperusteet-service/api/perusteet?sivukoko=100&suoritustapa=ops' ), true );
	echo "<table border='1'><tr><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td></tr>";
	foreach ( $alat ['data'] as $ala ) {
		$perusteet = json_decode ( file_get_contents ( 'https://eperusteet.opintopolku.fi/eperusteet-service/api/perusteet/' . $ala ['id'] . '/kaikki' ), true );
		$koulutusalat = json_decode ( file_get_contents ( 'https://eperusteet.opintopolku.fi/eperusteet-service/api/koulutusalat' ), true );
		$tutkintonimikekoodit = json_decode ( file_get_contents ( 'https://eperusteet.opintopolku.fi/eperusteet-service/api/perusteet/' . $ala ['id'] . '/tutkintonimikekoodit' ) . true );
		
		echo "<tr><td>";
		echo $perusteet ['nimi'] [$GLOBALS ['kieli']] . "</td><td>";
		echo $perusteet ['diaarinumero'] . "</td><td>";
		if (empty ( $perusteet ['korvattavatDiaarinumerot'] )) {
			echo "</td><td>";
		}
		for($w = 0; $w < count ( $perusteet ['korvattavatDiaarinumerot'] ); $w ++) {
			echo $perusteet ['korvattavatDiaarinumerot'] [$w] . "</td><td>";
		}
		if (empty ( $task_array ['korvattavatDiaarinumerot'] ['1'] )) {
			echo "</td><td></td><td>";
		}
		echo $perusteet ['koulutukset'] ['0'] ['nimi'] [$GLOBALS ['kieli']] . "</td><td>";
		echo $perusteet ['koulutukset'] ['0'] ['koulutuskoodiArvo'] . "</td><td>";
		
		foreach ( $koulutusalat as $koulutusala ) {
			if ($perusteet ['koulutukset'] ['0'] ['koulutusalakoodi'] == $koulutusalat ['koodi']) {
				echo $koulutusalat ['nimi'] ['fi'] . "</td><td>";
				foreach ( $koulutusala ['opintoalat'] as $opintoalat ) {
					if ($perusteet ['koulutukset'] ['0'] ['opintoalakoodi'] == $opintoalat ['koodi']) {
						echo $opintoalat ['nimi'] ['fi'] . "";
					}
				}
			}
		}
		foreach ( $perusteet ['osaamisalat'] as $osaamisalat ) {
			echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>";
			echo $osaamisalat ['nimi'] ['fi'];
			echo "</td><td>";
			echo $osaamisalat ['arvo'];
			echo "</td>";
			foreach ( $tutkintonimikekoodit as $tutkintonimikekoodi ) {
				echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>";
				if ($tutkintonimikekoodi ['osaamisalaArvo'] == $osaamisalat ['arvo']) {
					$tutkintonimikeArvo = $tutkintonimikekoodi ['tutkintonimikeArvo'];
					foreach ($tutkintonimikekoodi as $tutkintonimikeArvo){
						if ($tutkintonimikeArvo [$tutkintonimikeArvo] ['metadata'] [$b] ['kieli'] == "FI") {
							echo "<td>" . $tutkintonimikekoodit [$p] ['b'] [$tutkintonimikeArvo] ['metadata'] [$b] ['nimi'] . "</td>";
							echo "<td>" . $tutkintonimikeArvo . "</td>";
					}
				}
			}
		}
	}
}
}
function kaikkitutkinnonosat() {
	$alan = $_GET['ala'];
	echo "<a href='" . $_SERVER ['PHP_SELF'] . "'>alkuun</a>";
	foreach ($alan as $ala){
		$perusteet = json_decode ( file_get_contents ( 'https://eperusteet.opintopolku.fi/eperusteet-service/api/perusteet/' . $ala . '/kaikki' ), true );
		foreach ($perusteet['tutkinnonOsat'] as $tutkinnonOsa){
			echo "<td>".$tutkinnonOsa ['koodiArvo']. "<td>";
			echo "<td>".$tutkinnonOsa ['nimi'] [$GLOBALS['kieli']]. "<td>";
			echo "<td>".$tutkinnonOsa ['opintoluokitus']. "<td>";
			$GLOBALS['yli'] = $tutkinnonOsa ['id'];
			k_rakenne ($ala);
			echo $GLOBALS['llaajuus'] . "<td></td>";
			if ($GLOBALS['lpakollinen'] == "<td>pakollinen</td>"){
				echo $GLOBALS['lpakollinen'];
			}else{
				echo "<td>valinnainen</td>";
			}
			
			$GLOBALS['llaajuus'] = null;
			$GLOBALS['lpakollinen'] = null;
			echo "</tr>";
		}
	}
}
function k_rakenne($ala) {
	$perusteet = json_decode ( file_get_contents ( 'https://eperusteet.opintopolku.fi/eperusteet-service/api/perusteet/' . $ala . '/kaikki' ), true );
	if ($perusteet ['suoritustavat'] ['0'] ['suoritustapakoodi'] == "ops") {
		$ops = 0;
	} else {
		$ops = 1;
	}
	$osat ['osat'] = $perusteet ['suoritustavat'] [$ops] ['rakenne'] ['osat'];
	$kierros = 0;
	$ylinkohta = 0;

	k_rakenne_osat ( $osat, $ops, $perusteet );
}
function k_rakenne_osat($osat, $ops, $perusteet) {
	// var_dump($osat);
	foreach ( $osat ['osat'] as $osa ) {
		if (isset ( $osa ['_tutkinnonOsaViite'] )) {
			$GLOBALS ['ylinkohta'] = $GLOBALS ['kierros'];
			//echo "<tr>";
			for($a = 0; $a < $GLOBALS ['ylinkohta']; $a ++) {
				//echo "</td><td>";
			}
			//echo "<td></td>";
			k_tutkinnonOsaViitteet ( $ops, $perusteet, $osa ['_tutkinnonOsaViite'], $osa ['pakollinen'] );
		} else {
			//echo "<tr>";
			if ($GLOBALS ['ylinkohta'] > 0) {
				for($a = 0; $a < $GLOBALS ['ylinkohta']; $a ++) {
					//echo "</td><td>";
				}
			} else {
				for($a = 0; $a < $GLOBALS ['kierros']; $a ++) {
					//echo "</td><td>";
				}
				$GLOBALS ['kierros'] = $GLOBALS ['kierros'] + 1;
			}
			//echo "<td>" . $osa ['nimi'] [$GLOBALS ['kieli']] . "</td></tr>";
			k_rakenne_osat ( $osa, $ops, $perusteet );
		}
	}
}
function k_tutkinnonOsaViitteet($ops, $perusteet, $tutkinnonOsaViite, $pakollinen) {
	foreach ( $perusteet ['suoritustavat'] [$ops] ['tutkinnonOsaViitteet'] as $tutkinnonOsaViitteet ) {
		if ($tutkinnonOsaViitteet ['id']  == $tutkinnonOsaViite) {
			k_tutkinnonOsat ( $ops, $perusteet, $tutkinnonOsaViitteet ['_tutkinnonOsa'], $tutkinnonOsaViitteet ['laajuus'], $pakollinen );
		}
	}
}
function k_tutkinnonOsat($ops, $perusteet, $tutkinnonOsaViite, $laajuus, $pakollinen) {
	foreach ( $perusteet ['tutkinnonOsat'] as $tutkinnonOsa ) {
		if ($tutkinnonOsa ['id'] == $tutkinnonOsaViite) {
			//echo "<td>" . $tutkinnonOsa ['nimi'] [$GLOBALS ['kieli']] . "</td>";
			//echo "<td>" . $laajuus . "</td>";
			//echo "<td>" . $tutkinnonOsa ['koodiArvo'] . "</td>";
			//echo "<td>" . $tutkinnonOsa ['opintoluokitus'] . "</td>";
			//echo "toimi".$GLOBALS['yli'];
			if($tutkinnonOsa ['id'] == $GLOBALS['yli']){
				
				$GLOBALS['llaajuus'] = "<td>".$laajuus . "</td>";
			if ($pakollinen == true) {
				$GLOBALS['lpakollinen'] = "<td>pakollinen</td>";
			} else {
				//echo "<td>valinnainen</td>";
			}
			}
		}
	}
}
function alat_rakisi() {
	$alat = json_decode ( file_get_contents ( 'https://virkailija.opintopolku.fi/eperusteet-service/api/perusteet?sivukoko=100&suoritustapa=ops' ), true );
	echo "<tr><td><H1>Alat</H1></td><tr>";
	echo "<tr><td><button type='submit'>listaa</button>";
	echo "<a href='" . $_SERVER ['PHP_SELF'] . "'>alkuun</a>";
	echo "</td></tr>";
	foreach ( $alat ['data'] as $ala ) {
		echo "<tr><td><input type='checkbox' name='ala[]' value='";
		echo $ala ['id'];
		echo "'>";
		echo $ala ['nimi'] [$GLOBALS['kieli']];
		echo "</a></td></tr>";
	}
	
}
?>
</body>
</html>