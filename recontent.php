<?php

if ( isset($_POST['recontent']) ) {
	include('bdd.php');
	$query = "SELECT id, url, content, title, artdate FROM astropix_articles WHERE content = '' ORDER BY artdate DESC LIMIT 0,".$exec."";
	$req = $bdd->prepare($query);

	$i = 0;
	try { $req->execute(); echo '*Envoi de la requ&ecirc;te &agrave; la base de donn&eacute;es.'."<br />\n"; }
	catch (Exception $e) { echo "Erreur : <br />\n".$e; }

	while ( $data = $req->fetch() ) {
		$donnees[$i]['id'] = $data['id'];
		$donnees[$i]['url'] = $data['url'];
		$donnees[$i]['title'] = $data['title'];
		$donnees[$i]['content'] = $data['content'];
		$i++;
	} // end while data req->fetch
	unset($data);
	echo '*Cr&eacute;ation de la table &agrave; partir du r&eacute;sultat.'."<br />\n";
	$req->closeCursor();

	echo '*V&eacute;rification de la pr&eacute;sence des donn&eacute;es, et t&eacute;l&eacute;chargement des urls.'."<br />\n";
	$nbrequetes = 0;

	if ( isset($donnees) ) {
		foreach ( $donnees as $key => $val ) {
			if ( strlen($val['content']) > 1 ) {
				echo '*<img src="cross.png" alt="" /> Informations d&eacute;j&agrave; r&eacute;cup&eacute;r&eacute;es pour l\'article '.$val['id'].' - <u>'.$val['title']."</u><br />\n";
			} // end if verif img, credits et content < ou > à 1
			elseif ($nbrequetes >= $exec) { break; }
			else {
				$nbrequetes++;
				$fileresult = implode("",file($val['url']));
				$result = str_replace('<a href="archivepix.html">Discover the cosmos!</a>','',$fileresult);

				// Récupérations du contenu
				$result = preg_match('#<b> Explanation: </b>#isU', $result)
					?preg_replace('#^[^§]*<b> Explanation: </b>([^§]+)<p>[^§]*$#isU', '$1', $result)
					:$result;
				// Fin récupération du contenu

				$result = preg_replace('#\n#isU', ' ', $result);
				$result = preg_replace('#\t#isU', ' ', $result);
				$result = preg_replace('#\s\s+#isU', ' ', $result);
				$result = preg_replace('#,(.[^0-9])#isU', ', $1', $result);
				$result = preg_replace('#(\.|\?|!)([^h][^t][^m])#isU', '$1<br />'.strtoupper('$2'), $result);
				$result = preg_replace('#([a-zA-Z])<#isU', '$1 <', $result);
				$result = preg_replace('#^.*Explanation:#isU', '', $result);
				$result = preg_replace('#^ *</?[a-zA-Z0-9]+>#isU', '', $result);
				$result = preg_replace('#<hr>.*$#isU', '', $result);
				$result = preg_replace('#<[^a][^a][a-zA-Z0-9]*>#isU', '', $result);
				$result = preg_replace('#</?p[^§]*>#isU', '', $result);
				$result = preg_replace('# $#isU', '', $result);
				$result = preg_replace('#<br />$#isU', '', $result);
				$result = preg_replace('#href="ap#', 'href="http://apod.nasa.gov/apod/ap', $result);

				$query = "UPDATE astropix_articles SET content = :result WHERE id = :id";
				$req = $bdd->prepare($query);

				try {
					$req->execute(array('result'=>$result,'id'=>$val['id']));
					echo '<img src="check.png" alt="" /> R&eacute;ussi pour l\'article '.$val['id'].' - '.$val['title']."<br />\n";
				}
				catch (Exception $e) { echo 'Erreur pour l\'article '.$val['id'].'<br />'.htmlspecialchars($e)."<br /><br />\n"; }

			} // end if $key > exec
		} // end foreach donnees as key => val
	} // end if $donnees

	echo '*'.$nbrequetes.' requ&ecirc;tes ont &eacute;t&eacute; effectu&eacute;es.'."<br />\n";
	echo '*Fin du programme.'."<br />\n";

} // end if isset recontent

?>