<?php

if ( isset($_POST['stockage']) ) {
	if ( $_POST['stockage'] == 'Stockage' ) {
		if ( file_exists("final.log") ) {
			include('bdd.php');
			echo "\t"."*Ouverture du fichier<br />\n";
			$fopen = file("final.log");
			echo "\t\t".'*Fichier ouvert'."<br />\n";
			$tablekey = 0;
			$table = array();
			foreach ( $fopen as $key => $val ) {
				if ( strlen($val) > 5 ) {
					$table[$tablekey] = preg_replace("#\n#",'',$val);
					$tabdate	= preg_replace("#^([^:]+).*$#",'$1', $table[$tablekey]);
					$tabday		= preg_replace('#^.*( [0-9]{2} ).*$#', '$1', $tabdate);
					$tabyear	= preg_replace('#^.*( [0-9]{4} ).*$#', '$1', $tabdate);
					$tabmonth	= preg_replace('#^.*( [a-zA-Z]{3,9} ).*$#', '$1', $tabdate);
					$tabmonth	= str_replace('January', '01', $tabmonth);
					$tabmonth	= str_replace('February', '02', $tabmonth);
					$tabmonth	= str_replace('March', '03', $tabmonth);
					$tabmonth	= str_replace('April', '04', $tabmonth);
					$tabmonth	= str_replace('May', '05', $tabmonth);
					$tabmonth	= str_replace('June', '06', $tabmonth);
					$tabmonth	= str_replace('July', '07', $tabmonth);
					$tabmonth	= str_replace('August', '08', $tabmonth);
					$tabmonth	= str_replace('September', '09', $tabmonth);
					$tabmonth	= str_replace('October', '10', $tabmonth);
					$tabmonth	= str_replace('November', '11', $tabmonth);
					$tabmonth	= str_replace('December', '12', $tabmonth);
					$tabmatrix[$tablekey]['artdate']	= preg_replace("#\s#", '', $tabyear.'-'.$tabmonth.'-'.$tabday);
					$tabmatrix[$tablekey]['title']	= preg_replace('#.*<a.*>(.+)</a>.*$#', '$1', $val);
					$tabmatrix[$tablekey]['url']	= preg_replace('#.*<a href="(.+)".*$#', '$1', $val);
					$tabmatrix[$tablekey]['title']	= preg_replace('#(\s$)|(^\s)|(\n)#', '', $tabmatrix[$tablekey]['title']);
					$tabmatrix[$tablekey]['url']	= preg_replace('#(\s$)|(^\s)|(\n)#', '', $tabmatrix[$tablekey]['url']);
					$tablekey ++;
				} // end if strlen val > 5
			} // end foreach
			$fopen = '';

			echo "\t\t".'*Cr&eacute;ation de la table de donn&eacute;es &agrave; partir de la base de donn&eacute;s'."<br />\n";
			$data = '';
			$req = '';
			$i = 0;
			echo '*Envoi de la requ&ecirc;te &agrave; la BDD et r&eacute;cup&eacute;ration des informations.';
			echo "<br />\n"; 
			echo '*V&eacute;rification de la concordance des donn&eacute;s existantes.';
			echo "<br />\n"; 
			foreach ( $tabmatrix as $tabmtxkey => $tabmtxval ) {
				if ( $i < $exec ) {
					$query = 'SELECT * FROM astropix_articles WHERE url = :taburl';
					$req = $bdd->prepare($query);

					try	{ $req->execute(array('taburl'=>$tabmtxval['url'])); $key = 1;}
					catch (Exception $e) { echo "Erreur : <br />\n".$e; }
					if ( $data = $req->fetch() ) {
							echo "\n\t\t".'* <img src="cross.png" alt="" />';
							echo " Article existe d&eacute;j&agrave; :";
							echo ' Date : '.$tabmtxval['artdate'].' Titre : <strong>'.$tabmtxval['title'].'</strong>'."<br />\n";
					} // end if data = req->fetch() (s'il trouve url existante)
					else {
						$query = "INSERT INTO astropix_articles (id, title, url, artdate) VALUES ('',:title, :url, :artdate);";
						$req = $bdd->prepare($query);
						try { $req->execute(array('title'=>$tabmtxval['title'],'url'=>$tabmtxval['url'],'artdate'=>$tabmtxval['artdate']));
							echo "\t\t";
							echo '* <img src="check.png" alt="" /> Ecriture r&eacute;ussie pour l\'article <strong>'.$tabmtxval['title'].'</strong> datant du '.$tabmtxval['artdate']."<br />\n"; }
						catch (Exception $e) { echo "Erreur : <br />\n".$e; }
					} // end else $data (s'il ne trouve pas l'entrée)
					$i++;
					$req->closeCursor();
				} // end if i < exec
				else { break; }
			} // end foreach boucle de la TABLE
			echo "\t\t".'*Fin du programme'."<br />\n";
			echo "\t\t".'*'.$i.' ex&eacute;cutions effectu&eacute;es'."<br />\n";

		} // end if file_exists final.log
	} // end if $_POST stockage = stockage
} // end if isset stockage
?>