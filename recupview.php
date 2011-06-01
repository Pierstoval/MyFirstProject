<?php

////////////////////////////// AFFICHAGE DES LIENS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	if ( isset($_POST['flux']) ) {
		if ( $_POST['flux'] == 'Afficher les liens présents dans le fichier log' ) {
				if ( @file_get_contents("final.log") ) {
					echo '*R&eacute;cup&eacute;ration des informations...'."<br />\n\t";
					$fp = fopen("final.log", "r");
					$fstat = fstat($fp);
					$filetime = $fstat['mtime'];
					fclose($fp);
					$filedate = date('j/m/Y à G:i:s',$filetime);
					$timestamp1 = time() - $dateoffset;

					echo '*Derni&egrave;re actualisation de la liste : <strong>'.$filedate.'</strong>'."<br />\n\t";
					$result = implode("",file("final.log"));
					if ( $result == '' ) {
						echo '*Le fichier est vide'."<br />\n\t";
						echo '*Fin de l\'instruction'."<br />\n\t";
					}
					else {
						$isint = intval($_POST['nombre'])?intval($_POST['nombre']):99999;
						if ( $isint > 1 ) {
							$pages = $isint + 1; 
							$countinit = count(explode('<br />',$result))-1;
							$tableau = explode('<br />',$result,$pages);
							$offset = $pages - 1;
							$tableau[$offset] = '';
							$countfin = count($tableau)-1;
							if ( $countfin < $countinit  ) {
								echo '*R&eacute;cup&eacute;ration des '.$countfin.' derniers articles stock&eacute;s'."<br />\n\t";
								echo '*Rappel - Nombre total d\'articles r&eacute;cup&eacute;rables dans la liste : '.$countinit."<br />\n\t";
							}
							else {
								echo '*'.$countfin.' articles sont disponibles, et '.$isint.' ont &eacute;t&eacute; demand&eacute;s'."<br />\n\t";
								echo '*Tous les articles seront r&eacute;cup&eacute;r&eacute;s ('.$countfin.')'."<br />\n\t";
							}
							foreach ($tableau as $key => $val) {
								$length = strlen($val);
								if ($length > 5) {
									$links["$key"] = $val;
								}
							}
							echo '*Liste : <br />';
							foreach ($links as $key => $val) {
								$realkey = $key + 1;
								echo $realkey.' - '.$val."<br />\n\t";
							}
							echo'*Termin&eacute;'."<br />\n";
						}
						else {
							$countinit = count(explode('<br />',$result))-1;
							echo'*Veuillez v&eacute;rifier le nombre d\'articles &agrave; r&eacute;cup&eacute;rer.'."<br />\n\t";
								echo '*Rappel - Nombre total d\'articles r&eacute;cup&eacute;rables dans la liste : '.$countinit."<br />\n\t";
							$tableau = explode('<br />',$result,2);
							$tableau[1] = ''; 
							foreach ($tableau as $key => $val) {
								$length = strlen($val);
								if ($length > 5) {
									$links["$key"] = $val;
								}
							}
							echo '*Dernier article stock&eacute; : <br />';
							foreach ($links as $key => $val) {
								echo '1 - '.$val."<br />\n\t";
							}
							echo'*Termin&eacute;'."<br />\n";
						}
					} // else ===> if ( $result = '' ) { echo '*Le fichier est vide'."<br />\n\t"; }

				} // if ( file_exists("export.log") ) {
		} // if ( $_POST['flux'] == 'Récupérer les liens' 
		else {echo '*Num&eacute;ro 2 des petits malin va...';}
	}
//\\\\\\\\\\\\\\\\\\\\\\\\\\\\ AFFICHAGE DES LIENS ///////////////////////////////

?>