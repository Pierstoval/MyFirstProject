<?php

////////////////////////////// RECUPERATION DE LA PREMIERE PAGE \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	if ( isset($_POST['prempage']) ) {
		if ( $_POST['prempage'] == "Liste" ) {
			if ( !file_exists("export.log")) { // vérif du fichier log
				echo '*Le fichier log est inexistant'."<br />\n\t";
				file_put_contents('export.log', '');
			}
			else {
				echo '*Le fichier log existe d&eacute;j&agrave;'."<br />\n\t";
				$fp = fopen("export.log", "r");
				$fstat = fstat($fp);
				$filetime = $fstat['mtime'];
				fclose($fp);
				$filedate = date('G:i:s',$filetime);
				$timestamp1 = time() - $dateoffset;
				if ( $filetime <= $timestamp1 ) {
					$result = @implode("",file("http://apod.nasa.gov/apod/archivepix.html"));
					if ( !$result ) { echo '*Le fichier distant est inaccessible'."<br />\n\t"; }
					else { echo '*Vidage du fichier'."<br />\n\t"; file_put_contents('export.log', ''); }
					////// Téléchargement du fichier distant
						if ( file_get_contents("http://apod.nasa.gov/apod/archivepix.html")) {
							echo'*Le fichier distant existe'."<br />\n\t";
							echo'*T&eacute;l&eacute;chargement du fichier : '.'http://apod.nasa.gov/apod/archivepix.html'."<br />\n\t";
							$result = implode("",file("http://apod.nasa.gov/apod/archivepix.html"));
							if ( file_get_contents("export.log")) { echo '*&Eacute;criture dans le fichier log'."<br />\n\t"; }
							file_put_contents('export.log', print_r($result, TRUE));
							echo '*Ecriture dans le fichier log r&eacute;ussie'."<br />\n\t";
							echo '*Page r&eacute;cup&eacute;r&eacute;e : <a href="export.log" target="_blank">export.log</a>'."<br />\n\t";
						}
						else {
							echo '*L\'url n\'a pas pu être atteinte'."<br />\n\t";
						}
					////// End téléchargement du fichier distant
					////// Découpage du fichier récupéré
						$result = implode("",file("export.log"));
						if ( $result == '' ) { echo '*Le fichier est vide'."<br />\n\t"; }
						else {
							$result = preg_replace( '#^<title> Astronomy Picture of the Day[^§]*<b>#isU', ' ', $result );
							$result = preg_replace( '#</b>[^§]*</body>#isU', '', $result );
							$result = preg_replace( "#\n#isU", "", $result );
							$result = preg_replace( "#\t#isU", "", $result );
							$result = preg_replace( "#\s\s+#isU", " ", $result );
							$result = preg_replace( '#<br>#isU', "<br />\n ", $result );
							$result = preg_replace( '#href="(ap[0-9]+)#', 'href="http://apod.nasa.gov/apod/$1', $result);
							$result = preg_replace( '#([0-9]):#isU', "$1 :", $result );
							file_put_contents('final.log', print_r($result, TRUE));
							echo '*D&eacute;coupage effectu&eacute;'."<br />\n\t";
							echo '*Donn&eacute;es compil&eacute;es : <a href="final.log" target="_blank">final.log</a>'."<br />\n\t";
						}
					////// End découpage du fichier récupéré
					}
				else {
					echo'*Le fichier est trop r&eacute;cent :'."<br />\n\t";
					echo'*Récupéré à '.$filedate."<br />\n\t";
					echo'*Il y a ';
					$timeilya = time()-$filetime;
					if		( $timeilya < 60 ) { echo 'moins d\'une minute'; }
					elseif	( $timeilya >= 60 && $timeilya < 120 ) {
						$timeinminutes = floor($timeilya/60); echo $timeinminutes.' minute'; }
					elseif	( $timeilya >=120 && $timeilya < 3600 ) {
						$timeinminutes = floor($timeilya/60); echo $timeinminutes.' minutes'; }
					elseif	( $timeilya >= 3600 && $timeilya < 86400 ) {
						$timeinhours = floor($timeilya/3600); echo $timeinhours.' heure(s)';}
					echo "<br />\n\t";
					echo'*Rappel - D&eacute;lai minimum n&eacute;c&eacute;ssaire : '.$datexpl."<br />\n\t";
				}
				echo '*Termin&eacute;'."<br />\n\t";
			}
		}
		else {echo '*Num&eacute;ro 1 des petits malin va...';}
	} // end if ( isset($_POST['prempage'])
//\\\\\\\\\\\\\\\\\\\\\\\\\\\\ RECUPERATION DE LA PREMIERE PAGE ///////////////////////////////







?>