<?php

if ( isset($_POST['recimages']) ) {
	include('bdd.php');
	$query = 'SELECT * FROM astropix_articles WHERE img = \'\' ORDER BY id DESC LIMIT 0, '.intval($exec);
	$req = $bdd->prepare($query);

	$i = 0;
	try { $req->execute(); echo '*Envoi de la requ&ecirc;te &agrave; la base de donn&eacute;es.'."<br />\n"; }
	catch (Exception $e) { echo "Erreur : <br />\n".$e."<br />"; }

	$donnees = array(); 
	while ( $data = $req->fetch() ) {
		$donnees[$i]['id'] = $data['id'];
		$donnees[$i]['url'] = $data['url'];
		$donnees[$i]['img'] = $data['img'];
		$donnees[$i]['title'] = $data['title'];
		$i++;
	} // end while data req->fetch
	unset($data);
	echo '*Cr&eacute;ation de la table &agrave; partir du r&eacute;sultat.'."<br />\n";
	$req->closeCursor();

	echo '*V&eacute;rification de la pr&eacute;sence des donn&eacute;es, et t&eacute;l&eacute;chargement des urls.'."<br />\n";
	$nbrequetes = 0;
	foreach ( $donnees as $key => $val ) {
		if ($nbrequetes >= $exec) { break; }
		$nbrequetes++;

		$filexception = preg_match('#"#isU', $val['url'])?preg_replace('#".*$#isU', '', $val['url']):'';
		$fileresult = implode("",file($val['url']));
		$result = str_replace('<a href="archivepix.html">Discover the cosmos!</a>','',$fileresult);
		unset($fileresult);
		$result = preg_replace('#Explanation: </b>[^§]*$#isU', '', $result);

		// Récupération des images
			if ( preg_match('#<img.{,50}src="image/#isU', $result) ) {
				$result = preg_replace('#^[^§]*<img.*src="(image/[^"]+)"[^§]*$#isU', 'http://apod.nasa.gov/apod/$1', $result); }
			if ( preg_match('#<a href="image/#isU', $result) ) {
				$result = preg_replace('#^[^§]*<img.*src="(image/[^"]+)"[^§]*$#isU', 'http://apod.nasa.gov/apod/$1', $result); }
			elseif ( preg_match('#<a href="http://www.youtube\.com/watch\?v=#isU', $result) ) {
				$result = preg_replace('#^[^§]*<a href="(http://(www\.)?youtube\.com/watch\?v=[^"\']+)"[^§]*$#isU', '$1', $result); }
			elseif ( preg_match('#<iframe.*src=("|\')#isU', $result) ) {
				$result = preg_replace('#^[^§]*<iframe[^§]*src=("|\')(.+)("|\')[^§]*$#isU', '$2', $result);
				$result = preg_replace('#\?.*$#','',$result); }
			elseif ( preg_match('#<embed.*src="#isU', $result) ) {
				$result = preg_replace('#^[^§]*<embed[^§]*src=("|\')(.+)("|\')[^§]*$#isU', '$2', $result);
				$result = preg_replace('#\?.*$#','',$result); }
			else {
				$result = preg_replace('#^[^§]*<img src="([^"]+)"[^§]*$#isU', '$1', $result); }
		// Fin récupération des images ou vidéos
		
		if (preg_match('#youtube.com/embed/#isU', $result)) {
			$result = preg_replace('#youtube\.com/embed/#isU', 'youtube.com/watch?v=', $result);
		}
		if (preg_match('#player\.vimeo\.com/video/#isU', $result)) {
			$result = preg_replace('#player\.vimeo\.com/video/#isU', 'vimeo.com/', $result);
		}
		if (preg_match('#image/1105/SupernovaSonata_parker900\.jpg#isU', $result)) {
			$result = 'http://www.astro.uvic.ca/~alexhp/new/supernova_sonata.html';
		}
		if (preg_match('#^image/(.+)$#isU', $result)) {
			$result = preg_replace('#^image/(.+)$#isU', 'http://apod.nasa.gov/apod/image/$1', $result); 
		}

		$result = preg_replace('#(\s|\n|\t)#isU','',$result);
		if ( preg_match('#Astronomy ?Picture ?of ?the ?Day#isU', $result) ) {
			echo '*<img src="cross.png" alt="" /> Erreur #1 lors de l\'enregistrement de l\'article : ';
			echo $val['id'].' - '.$val['title'];
			echo '<br />'."\n";
		}
		else {
			$query = "UPDATE astropix_articles SET img = '".$result."' WHERE id = '".$val['id']."'";
			$req = $bdd->prepare($query);
	
			try {
				$req->execute();
				echo '*<img src="check.png" alt="" /> Enregistrement de l\'image (<span class="txtlitl" ';
				if (preg_match('#apod/image/#isU', $result)){ echo 'style="color:#6CF;"'; }
				elseif (preg_match('#youtube#isU', $result)){ echo 'style="color:#F66;"'; }
				elseif (preg_match('#vimeo#isU', $result)){ echo 'style="color:#693;"'; }
				echo '> '.$result.' </span>) pour l\'article n&deg;<u>'.$val['id']."</u><br />\n";
			}
			catch (Exception $e) {
				echo '*<img src="cross.png" alt="" /> Erreur #2 lors de l\'enregistrement de l\'article : '.$val['id'];
				echo '<br />**Contenu : '.$result."<br />\n".$e."<br /><br />"; }
		}

	} // end foreach donnees as key => val
	echo '*'.$nbrequetes.' requ&ecirc;tes ont &eacute;t&eacute; effectu&eacute;es.'."<br />\n";
	echo '*Fin du programme.'."<br />\n";

} // end if isset recimages

?>