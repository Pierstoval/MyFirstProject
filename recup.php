<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>T&eacute;l&eacute;chargement d'astropix</title>
	<link href="styles.css" rel="styleSheet" type="text/css" />
	<script type="text/javascript"><!--
		function getexec() {
			nbexec = document.form1['nbexec'].value;
			document.form1['nombre'].value = nbexec;
		}
	--></script>
<?php include('params22.php'); ?>
</head>

<body onLoad="getexec();">
	<div id="left"><a href="recup.php"><img src="moon.jpg" alt="" /></a></div>
	<div id="right"><a href="view.php"><img src="moon.jpg" alt="" /></a></div>
<div id="content">
	<form name="form1" action="recup.php" method="post">
		<div class="divform"><table><tr><td style="font-size:10px;width:300px;">
			<input type="submit" name="prempage" value="Liste" /><br />
			<input type="submit" name="recimages" value="R&eacute;cup&eacute;rer les images" /><br />
			<input type="submit" name="recontent" value="R&eacute;cup&eacute;rer le contenu" /><br />
			<input type="submit" name="stockage" value="Stockage" />
		</td>
		<td style="font-size:10px;width:120px;">Nombre d'ex&eacute;cutions 
			<select name="nbexec" size="6" style="width:80px;" onChange="getexec();">
				<option value="tout">Tout</option>
				<option value="5">5</option>
				<option value="10" selected="selected">10</option>
				<option value="50">50</option>
				<option value="100">100</option>
				<option value="150">150</option>
			</select></td>
		<td style="font-size:11px;">Date de r&eacute;cup&eacute;ration du fichier log : <?php 
			$fp = @fopen("export.log", "r");
			$fstat = @fstat($fp);
			$filetime = $fstat['mtime'];
			$filedate = date('l d F G:i',$filetime);
			$filedate = str_replace('Monday',	'Lundi', $filedate);
			$filedate = str_replace('Tuesday',	'Mardi', $filedate);
			$filedate = str_replace('Wednesday','Mercredi', $filedate);
			$filedate = str_replace('Thursday',	'Jeudi', $filedate);
			$filedate = str_replace('Friday',	'Vendredi', $filedate);
			$filedate = str_replace('Saturday',	'Samedi', $filedate);
			$filedate = str_replace('Sunday',	'Dimanche', $filedate);
			$filedate = str_replace('January',	'Janvier', $filedate);
			$filedate = str_replace('February',	'F&eacute;vrier', $filedate);
			$filedate = str_replace('March',	'Mars', $filedate);
			$filedate = str_replace('April',	'Avril', $filedate);
			$filedate = str_replace('May',		'Mai', $filedate);
			$filedate = str_replace('June',		'Juin', $filedate);
			$filedate = str_replace('July',		'Juillet', $filedate);
			$filedate = str_replace('August',	'Ao&ucirc;t', $filedate);
			$filedate = str_replace('September','Septembre', $filedate);
			$filedate = str_replace('October',	'Octobre', $filedate);
			$filedate = str_replace('November',	'Novembre', $filedate);
			$filedate = str_replace('December',	'D&eacute;cembre', $filedate);
			echo "<br />\n".$filedate;
			echo'<br />'."\n".'Il y a ';
			$timeilya = time()-$filetime;
			if		( $timeilya < 60 ) { echo 'moins d\'une minute'; }
			elseif	( $timeilya >= 60 && $timeilya < 120 ) { $timeinminutes = floor($timeilya/60); echo $timeinminutes.'minute'; }
			elseif	( $timeilya >=120 && $timeilya < 3600 ) { $timeinminutes = floor($timeilya/60); echo $timeinminutes.'minutes'; }
			elseif	( $timeilya >= 3600 ) { $timeinhours = floor($timeilya/3600); echo $timeinhours.'h';}
		?></td></tr></table></div>
		<div class="divform">
			<!--Nombre de liens &agrave; r&eacute;cup&eacute;rer : <br />
			<span style="font-size:10px">(nombre sup&eacute;rieur à 1)</span><br />-->
			<input type="hidden" name="nombre" class="inputxt" value="" />
			<!--<span style="font-size:10px">(Si vous n'entrez rien, le dernier article sera r&eacute;cup&eacute;r&eacute;)</span><br />-->
			<input type="submit" name="flux" value="Afficher les liens pr&eacute;sents dans le fichier log" />
		</div>
	</form>

<?php


if (isset($_POST['nombre'])){
	echo "\n\t".'<div id="petitcontenu">'."\n\t"; $timedebut = time();
} // fin balise du contenu et dur&eacute;e ex&eacute;cution

	

	if ( isset($_POST['prempage']))	{ include('recupe.php');}
	if ( isset($_POST['flux']))		{ include('recupview.php'); }
	if ( isset($_POST['stockage']))	{ include('stockage.php'); }
	if ( isset($_POST['recimages'])){ include('recimgs.php'); }
	if ( isset($_POST['recontent'])){ include('recontent.php'); }



if ( isset($_POST['nombre']))	{
	$timefin = time() - $timedebut;
	echo "\t\t".'*Programme ex&eacute;cut&eacute; en '.$timefin.' seconde';
	if ( $timefin > 1) { echo 's'; }
	echo '.'."\n";
	echo "\n\t</div> <!-- /div petitcontenu -->\n\n";
} // fin balise du contenu et dur&eacute;e ex&eacute;cution

?>

</div> <!-- /div content -->

<div>
	
</div>
</body>
</html>