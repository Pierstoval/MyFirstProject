<?php
include('bdd.php');

$dateoffset = 7200; // délai pour retélécharger/afficher le fichier


date_default_timezone_set('Europe/Paris');


//			Vérification du nombre d'exécutions
$exec = 32767;
if ( isset($_GET['nbexec']) ) {
	if (	$_GET['nbexec'] == '5'
		||	$_GET['nbexec'] == '10'
		||	$_GET['nbexec'] == '50'
		||	$_GET['nbexec'] == '100'
		||	$_GET['nbexec'] == '150' ) {
		$exec = intval($_GET['nbexec']);
	}
	else { $exec = 32767; }
}
//			Fin Vérification du nombre d'exécutions




/*---------------------DATE EXPLICATION---------------------*/
$secondes = 0;
$minutes = 0;
$heure = 0;
$minutes = $dateoffset/60;
$secondes = bcmod($dateoffset,"60");
$minutes = floor($minutes);
while($secondes >= "60") {
	$secondes = $secondes-60;
	$minutes++; }
while($minutes >= "60") {
	$minutes = $minutes-60;
	$heure++; }

if($minutes == "0") { $minutes = ''; }
if($minutes > "0") { $minutes = $minutes.'mn '; }

if($secondes == "0") { $secondes = ''; }
if($secondes > "0") { $secondes = $secondes.'s'; }

if($heure == "0") { $heure = ''; }
if($heure > "0") { $heure = $heure.'h '; }

$datexpl = $heure.$minutes.$secondes;
/*-------------------FIN DATE EXPLICATION-------------------*/






/*--------------------------------------------------------------------------------*/
/*----------------------------- Paramètres du viewer -----------------------------*/
/*--------------------------------------------------------------------------------*/
$ascdesc = isset($_GET['ascdesc'])?$_GET['ascdesc']:'desc';
switch($ascdesc){
	case'asc': case'desc': break;
	default: $ascdesc='asc'; }

$tri = isset($_GET['tri'])?$_GET['tri']:'artdate';
switch($tri){
	case'artdate':case'title':break;
	default:$tri='date'; }

$nbexec = isset($_GET['nbexec'])?$_GET['nbexec']:16;
switch($nbexec){
	case'8':	$nbexec=8; break;
	case'16':	$nbexec=16; break;
	case'24':	$nbexec=24; break;
	case'32':	$nbexec=32; break;
	default:	$nbexec=16; }
	
// $tags = isset($_GET['tags'])?$_GET['tags']:'';
$tags = '';
switch($tags){
	default:		$tags = true;
	case 'true':	$tags = true; break;
	case '':		$tags = ''; }

$viewimgs = isset($_GET['viewimgs'])?$_GET['viewimgs']:'';
switch($viewimgs){
	default:		$viewimgs = true;
	case 'true':	$viewimgs = true; break;
	case '':		$viewimgs = ''; }

$contitl = isset($_GET['contitl'])?$_GET['contitl']:'title';
switch($contitl){
	case'or':		$contitl = 'or'; break;
	case'and':		$contitl = 'and'; break;
	case'content':	$contitl = 'content'; break;
	case'title':	$contitl = 'title'; break;
	default:		$contitl = 'title'; }

$terms = isset($_GET['terms'])?$_GET['terms']:'any';
switch($terms){
	case'all':		$terms = 'all'; break;
	case'any':		$terms = 'any'; break;
	default:		$contitl = 'any'; }


if(isset($_GET['query'])){if(strlen($_GET['query'])>1){$inputquery = isset($_GET['query'])?manageQuery($_GET['query']):'';echo'<!--'.$inputquery.'-->';}}


if ( isset($_GET['query']) ) {
	if ( strlen($_GET['query']) > 1 ) {
		$inputquery = isset($_GET['query'])?manageQuery($_GET['query']):'';
	}
	else { $inputquery = ''; }
}
else { $inputquery = ''; }





/*--------------------------------------------------------------------------------*/
/*----------------------------- FONCTIONS du viewer ------------------------------*/
/*--------------------------------------------------------------------------------*/

function convertdate ($datestamp) {
	$tablestamp = explode('-', $datestamp);
	$year	= intval($tablestamp[0]);
	$month	= $tablestamp[1];
	$day	= ($tablestamp[2]);

	$day	= preg_replace('#^0#', '', $day);

	$month	= str_replace('01','Janvier', $month);
	$month	= str_replace('02','F&eacute;vrier', $month);
	$month	= str_replace('03','Mars', $month);
	$month	= str_replace('04','Avril', $month);
	$month	= str_replace('05','Mai', $month);
	$month	= str_replace('06','Juin', $month);
	$month	= str_replace('07','Juillet', $month);
	$month	= str_replace('08','Ao&ucirc;t', $month);
	$month	= str_replace('09','Septembre', $month);
	$month	= str_replace('10','Octobre', $month);
	$month	= str_replace('11','Novembre', $month);
	$month	= str_replace('12','D&eacute;cembre', $month);

	$datestamp = $day.' '.$month.' '.$year;
	return $datestamp;
} // end function convertdate







function explodeQuery($var) {
	$explodetags = array(" ", ",", ".", "+", "'", '"', '-', '_', '(', ')', '{', '[', ']', '}', '@', '%', '$', '£', 'µ', '*', '`');
	$var = str_replace($explodetags, ' ', $var);
	$var = htmlspecialchars($var);
	$var = preg_replace('/\s\s+/is', ' ', $var);
	return $var;
} // end function explodevar







function manageQuery($input) {
	$input = str_replace('\"', '"', $input);
	$querycount = 1;
	$querylist = 0;
	$querytab = array();
	while ( preg_match('#(?:.*"){'.$querycount.'}([^"]+)".*$#isU', $input) ) {
		$querytab[$querylist] = preg_replace('#(?:.*"){'.$querycount.'}([^"]+)".*$#isU', '$1', $input);
		$querycount = $querycount + 2;
		$querylist++;
	}
	foreach ( $querytab as $key => $val ) {
		if ($querytab[$key] == $input) { unset($querytab[$key]); }
		else {
			$querytab[$key] = explodeQuery($querytab[$key]);
			$querytab[$key] = '%'.$querytab[$key].'%';
		}
	}
	if ( $querytab == array() ) { $querytab = ''; }

	$inputwthquote = preg_replace('#"[^"]+"#isU', '', $input);
	$inputwthquote = explodeQuery($inputwthquote);
	$inputs = explode(' ', $inputwthquote);
	foreach ( $inputs as $key => $val ) {
		if ( $val == '') { unset($inputs[$key]); }
		else {
			$inputs[$key] = preg_replace('#(^ | $)#','',$val);
			$inputs[$key] = '%'.$inputs[$key].'%';
		}
	}
	$finputs = implode(' ', $inputs);
	$fqueryt = '';
	if ( $querytab ) { $fqueryt = implode(' ', $querytab); }
	$checksum = $finputs.' '.$fqueryt;
	$checksum = explode('% %', $checksum);
	foreach($checksum as $key => $val) {
		$checksum[$key] = str_replace('%','',$val);
		$checksum[$key] = preg_replace('#(^ | $)#','',$checksum[$key]);
		$checksum[$key] = '%'.$checksum[$key].'%';
	}

	foreach ( $checksum as $key => $val ) {
		if ( $key == 0 ) { $logicword = 'WHERE'; }
		else {
			$terms = isset($_GET['terms'])?$_GET['terms']:'any';
			switch($terms){
				case'all': $logicword = 'AND';break;
				case'any': $logicword = 'OR';break;
				default: $logicword = 'OR';
			}
		}

		$contitl = isset($_GET['contitl'])?$_GET['contitl']:'title';
		switch($contitl){
			case'or':		$contitl = 'or'; break;
			case'and':		$contitl = 'and'; break;
			case'content':	$contitl = 'content'; break;
			case'title':	$contitl = 'title'; break;
			default:		$contitl = 'title'; }

		if ( $contitl == 'or' ) { $querypart[$key] = $logicword.' (`title` LIKE \''.$val.'\' OR `content` LIKE \''.$val.'\')'; }
		elseif ( $contitl == 'and' ) { $querypart[$key] = $logicword.' (`title` LIKE \''.$val.'\' AND `content` LIKE \''.$val.'\')'; }
		elseif ( $contitl == 'content' ) { $querypart[$key] = $logicword.' (`content` LIKE \''.$val.'\')'; }
		elseif ( $contitl == 'title' ) { $querypart[$key] = $logicword.' (`title` LIKE \''.$val.'\')'; }
		else { $querypart[$key] = $logicword.' (`title` LIKE \''.$val.'\')'; }

	} // end foreach checksum
	$finalquery = implode(' ', $querypart);
	return $finalquery;

} // end function manageQuery


/*--------------------------------------------------------------------------------*/
/*---------------------------- END FONCTIONS du viewer ---------------------------*/
/*--------------------------------------------------------------------------------*/


function traiteurl($url) {
	$url= strtr($url, 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ', 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn');
	$url= str_replace(" ","-",$url);
	$url= mb_strtolower(preg_replace('#[[:space:]\']#', "-", $url), 'UTF-8');
	$url= preg_replace('#-{2,3}#','-',$url);
	$url= str_replace("%20","-",$url);
	$url= str_replace("?","",$url);		$url= str_replace(",","",$url);		$url= str_replace("!","",$url);
	$url= str_replace(":","",$url);		$url= str_replace("%","",$url);		$url= str_replace(".","",$url);
	$url= str_replace("/","",$url);		$url= str_replace("\\","",$url);	$url= str_replace("§","",$url);
	$url= str_replace("*","",$url);		$url= str_replace("µ","",$url);		$url= str_replace("^^","",$url);
	$url= str_replace("^","",$url);		$url= str_replace("$","",$url);		$url= str_replace("£","",$url);
	$url= str_replace("¤","",$url);		$url= str_replace("~~","",$url);	$url= str_replace("~","",$url);
	$url= str_replace('"',"",$url);		$url= str_replace("'","",$url);		$url= str_replace("#","",$url);
	$url= str_replace("(","",$url);		$url= str_replace(")","",$url);		$url= str_replace("=","",$url);
	$url= str_replace("{","",$url);		$url= str_replace("[","",$url);		$url= str_replace("|","",$url);
	$url= str_replace("``",'',$url);	$url= str_replace("`","",$url);		$url= str_replace("@","",$url);
	$url= str_replace("]","",$url);		$url= str_replace("}","",$url);		$url= str_replace("°","",$url);
	$url= str_replace("<","",$url);		$url= str_replace(">","",$url);		$url= str_replace("²","",$url);
	$url= str_replace("¨¨","",$url);	$url= str_replace("¨","",$url);		$url= str_replace("__","_",$url);
	$url= str_replace("--","-",$url);	$url= str_replace("-","_",$url);
	$url = mb_strtolower($url, 'UTF-8');
	return $url;
}
