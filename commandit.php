<?php
	function get_microtime(){
		list($tps_usec, $tps_sec) = explode(" ",microtime());
		return ((float)$tps_usec + (float)$tps_sec);
	}
	$tps_start = get_microtime();


	$url = 'http://astro.pierstoval.com/recup.php';
	$fields1 = 'nbexec=5&nombre=5&prempage=Liste';
	$fields2 = 'nbexec=5&nombre=5&stockage=Stockage';
	$fields3 = 'nbexec=5&nombre=5&recontent=R&eacute;cup&eacute;rer le contenu';
	$fields4 = 'nbexec=5&nombre=5&recimages=R&eacute;cup&eacute;rer les images';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields1); curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, TRUE); curl_exec($ch); curl_close($ch);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields2); curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, TRUE); curl_exec($ch); curl_close($ch);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields3); curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, TRUE); curl_exec($ch); curl_close($ch);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields4); curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, TRUE); curl_exec($ch); curl_close($ch);

	$tps_end = get_microtime();
	$tps = $tps_end - $tps_start;

	$tps = strval($tps);
	$tps = substr($tps, 0, 6);
	//echo '<div>R&eacute;ussi !<br />Temps d\'ex&eacute;cution : '.$tps.' secondes<br /><a href="view.php">Retour au site dans 5 secondes...</a></div>';
	header('Location: view.php');
	$f = fopen('commandit_log.txt', 'a');
	fwrite($f, "\nDate=>".date(DATE_RFC822).'|IP=>'.$_SERVER['REMOTE_ADDR'].'|REFERER=>'.$_SERVER['HTTP_REFERER'].'|Exec=>'.$tps.'s');
	fclose($f);

