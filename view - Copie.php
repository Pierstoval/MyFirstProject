<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Astronomy Picture of the Day</title>
	<link href="styles.css" rel="styleSheet" type="text/css" />
	<script type="text/javascript" src="functions.js"></script>
<?php require('params.php'); require('functions.php'); ?>
</head>

<body>
	<div id="contentview">
		<form action="view.php" method="post" name="astropix">
			<table id="menuview">
				<tr>
					<th colspan="4" class="divview">
						<input type="hidden" name="page" value="" /><?php
							$pg = isset($_POST['page'])?intval($_POST['page']):1;
							$actualquery = '';
							$input = '';
							if (isset($_POST['actualquery'])) {
								$actualquery = $_POST['query'];
								$actualquery = str_replace('\\', '', $actualquery);
								$actualquery = str_replace('"', '&quot;', $actualquery);
								$actualquery = preg_replace('#\n#isU', ' ', $actualquery);
								$actualquery = preg_replace('#\t#isU', ' ', $actualquery);
								$actualquery = preg_replace('#\s\s+#isU', ' ', $actualquery);
							}
							if (isset($_POST['query'])) {
								$input = $_POST['query'];
								$input = str_replace('\\', '', $input);
								$input = str_replace('"', '&quot;', $input);
								$input = preg_replace('#\n#isU', ' ', $input);
								$input = preg_replace('#\t#isU', ' ', $input);
								$input = preg_replace('#\s\s+#isU', ' ', $input);
								echo'<input type="hidden" name="actualquery" value="'.$input.'" />'."\n";
							}
							if ( $actualquery != $input ) { $pg = 1; unset($actualquery); }
							else { echo"\n"; }
						?>
						<input type="button" value="R&eacute;initialiser les crit&egrave;res" onclick="window.location.href='view.php';" id="critere" />
						<?php
							$query = 'SELECT `artdate` FROM `astropix_articles` ORDER BY `astropix_articles`.`artdate` DESC LIMIT 0 , 1';
							$req = $bdd->prepare($query);
							try { $req->execute(); }
							catch (Exception $e) { echo "Erreur : <br />\n".$e; }
							$lastone = $req->fetch();
							$today = date('Y-m-d');
							if ($lastone[0] != $today) {
								echo '<p id="clicktoday">L\'article d\'aujourd\'hui n\'est pas affich&eacute ? Cliquez <a href="commandit.php">ICI</a> !<br />(Cette action peut prendre plusieurs secondes, merci de ne cliquer qu\'UNE SEULE FOIS.)</p>';
							}
							$req->closeCursor();
							
						?>
					</th>
				</tr>
				<tr>
					<td class="divview div1">
						Mot(s)-cl&eacute;(s) : <input type="text" name="query" id="keywords" <?php
							if (isset($_POST['query'])) { echo'value="'.$input.'" '; }
						?>/> &nbsp; 
						<input type="submit" value="Rechercher !" /><br />
						<select name="contitl" class="contitl" onchange="this.form.submit();">
							<option value="title"<?php if($contitl=='title'){echo' selected';}?>>Dans le titre</option>
							<option value="content"<?php if($contitl=='content'){echo' selected';}?>>Dans le contenu</option>
							<option value="and"<?php if($contitl=='and'){echo' selected';}?>>Dans le titre ET le contenu</option>
							<option value="or"<?php if($contitl=='or'){echo' selected';}?>>Dans le titre OU le contenu</option>
						</select>
						<select name="terms" class="terms" onchange="this.form.submit();">
							<option value="any"<?php if($terms=='any'){echo' selected';}?>>Au moins un des termes</option>
							<option value="all"<?php if($terms=='all'){echo' selected';}?>>Tous les termes</option>
						</select>
					</td>
					<td class="divview div2"><label for="tri">Trier par :</label>
						<select name="tri" id="tri" size="1" onchange="this.form.submit();">
							<option value="artdate"<?php if($tri == 'date'){echo' selected';} ?>>Date</option>
							<option value="title"<?php if($tri == 'title'){echo' selected';} ?>>Titre</option>
						</select><br />
						<input type="checkbox" name="viewimgs" value="true" id="viewimgs" onchange="this.form.submit();" <?php
						if($viewimgs == true) { echo'checked '; } ?>/> &nbsp; <label for="viewimgs">Ne pas afficher les images</label>
					</td>
					<td class="divview div3">
						<input id="asc" type="radio" name="ascdesc" value="asc" onchange="this.form.submit();" <?php
						if($ascdesc == 'asc'){echo'checked ';} ?>/><label for="asc">Croissant</label><br />
						<input id="desc" type="radio" name="ascdesc" value="desc" onchange="this.form.submit();" <?php
						if($ascdesc == 'desc'){echo'checked ';} ?>/><label for="desc">D&eacute;croissant</label>
					</td>
					<td class="divview div4">
						<select name="nbexec" onchange="this.form.submit();">
							<option value="8"<?php if($nbexec == 8){echo' selected';} ?>>8</option>
							<option value="16"<?php if($nbexec == 16){echo' selected';} ?>>16</option>
							<option value="24"<?php if($nbexec == 24){echo' selected';} ?>>24</option>
							<option value="32"<?php if($nbexec == 32){echo' selected';} ?>>32</option>
						</select> &nbsp; R&eacute;sultats<br />
						<input type="checkbox" name="tags" id="tags" value="true" onchange="this.form.submit();" <?php
						if($tags == true) { echo'checked '; } ?>/> &nbsp; <label for="tags">Voir les tags</label>
					</td>
				</tr>
			</table> <!-- /table menuview -->
		</form> <!-- /form -->
	</div> <!-- /div content -->
	<div id="contentview2">
	<?php
			$query = 'SELECT COUNT(*) FROM astropix_articles '.$inputquery;
			$req = $bdd->prepare($query);
			try { $req->execute(); }
			catch (Exception $e) { echo "Erreur : <br />\n".$e; }
			$nbfound = $req->fetch();
			$nbfound = $nbfound[0];
			$req->closeCursor();
			
			$query = '';
			if ( $pg == 1 ) { $start = 0; }
			elseif ($pg > 1) { $start = $nbexec * $pg - $nbexec; }
			else { $start = 0; }

			$query = 'SELECT * FROM astropix_articles '.$inputquery.' ORDER BY '.$tri.' '.$ascdesc.' LIMIT '.$start.', '.$nbexec;
			$req = $bdd->prepare($query);
			try { $req->execute(); }
			catch (Exception $e) { echo "Erreur : <br />\n".$e; }
	
			$i = 0;
			$donnees = array();
			while ( $data = $req->fetch() ) {
				$donnees[$i]['id'] = $data['id'];
				$donnees[$i]['title'] = htmlentities($data['title']);
				$donnees[$i]['url'] = $data['url'];
				$donnees[$i]['artdate'] = $data['artdate'];
				$donnees[$i]['img'] = $data['img'];
				$donnees[$i]['content'] = $data['content'];
				$i++;
			}
			$data = '';
			$query = '';


			/*-----------------------Début de l'affichage du nombre de requêtes-----------------------*/
			echo '<table>';
			echo '<tr><td class="divview" colspan="4">';
			if ($pg == 0) {
				if ( $nbfound > $nbexec ) { echo $nbexec.' r&eacute;sultats sur un total de '.$nbfound.' correspondant &agrave; la requ&ecirc;te.'; }
				else { echo $nbfound.' r&eacute;sultats correspondent &agrave; la requ&ecirc;te.'; }
			}
			else {
				$debstart = $start + 1;
				$finstart = $start + $nbexec;
				if ( $finstart > $nbfound ) { $finstart = $nbfound; }
				if ( $nbfound > $nbexec ) { echo 'R&eacute;sultats '.$debstart.' &agrave; '.$finstart.' sur un total de '.$nbfound.' articles correspondant &agrave; la requ&ecirc;te.'; }
				else { echo $nbfound.' r&eacute;sultats correspondent &agrave; la requ&ecirc;te.'; }
			}
			echo '</td></tr>';
			echo "\n\t\t";
			/*----------------------- Fin de l'affichage du nombre de requêtes -----------------------*/



			/*-----------------------Pagination-----------------------*/
				if($pg<1){$pg=1;}
				$nbpages = intval($nbfound/$nbexec)+1;

				if($nbpages != 1) {
					echo '<tr> <!-- Pagination -->'."\n\t\t\t".'<td class="divview" colspan="4">'."\n\t\t\t\t";
					if($pg>$nbpages){$pg=$nbpages;}
					if($pg>1)					{echo'<a href="javascript:pagination(';echo $pg-1;echo ');" class="paging">&lt; Pr&eacute;c&eacute;dent</a>'."\n\t\t\t\t"; }
					if($pg==1)					{echo'<span class="paging">1</span>'."\n\t\t\t\t"; }
					else						{echo'<a href="javascript:pagination(1);" class="paging">1</a>'."\n\t\t\t\t"; }
					if($pg>4)					{echo'<span class="paging">...</span>'."\n\t\t\t\t";} /* affichage des '...' */
					if($pg>3 && $pg<=$nbpages)	{echo'<a href="javascript:pagination(';echo$pg-2;echo');" class="paging">';echo$pg-2;echo'</a>'."\n\t\t\t\t"; }
					if($pg>2 && $pg<=$nbpages)	{echo'<a href="javascript:pagination(';echo$pg-1;echo');" class="paging">';echo$pg-1;echo'</a>'."\n\t\t\t\t"; }
					if($pg>1 && $pg<$nbpages)	{echo'<span class="paging">'.$pg.'</span>'."\n\t\t\t\t"; } /* PAGE EN COURS */
					if($pg>0 && $pg<$nbpages-1)	{echo'<a href="javascript:pagination(';echo$pg+1;echo');" class="paging">';echo$pg+1;echo'</a>'."\n\t\t\t\t"; }
					if($pg>0 && $pg<$nbpages-2)	{echo'<a href="javascript:pagination(';echo$pg+2;echo');" class="paging">';echo$pg+2;echo'</a>'."\n\t\t\t\t"; }
					if($pg<$nbpages-3)			{echo'<span class="paging">...</span>'."\n\t\t\t\t";} /* affichage des '...' */
					if($pg<$nbpages)			{echo'<a href="javascript:pagination('.$nbpages.');" class="paging">'.$nbpages.'</a>'."\n\t\t\t\t"; }
					else						{echo'<span class="paging">'.$nbpages.'</span>'."\n\t\t\t"; }
					if($pg<$nbpages)			{echo'<a href="javascript:pagination(';echo $pg+1; echo ');" class="paging">Suivant &gt;</a>'."\n\t\t\t"; }
					echo '</td>'."\n\t\t".'</tr> <!-- /Pagination -->'."\n\t\t";
				} //end if nbpages != 1
			/*---------------------Fin pagination---------------------*/



			/*---------- Début de l'affichage cellule par cellule, attention ceci est une boucle FOREACH ----------*/
			echo '<tr>'."\n";
			foreach ( $donnees as $key => $val ) {
				$artdate = convertdate($val['artdate']);
				$brtag = "\n\t\t\t\t";
				echo "\t\t\t".'<td class="divviewimg">'.$brtag;
				echo $artdate.$brtag;
				echo '<a href="'.$val['url'].'" target="_blank" class="artview';
				if ( mb_strlen($val['title']) > 35 ) { echo ' little'; } 
				if ( ($key+1) % 4 == 0 ) { echo ' viewleft'; }
				elseif( ($key+1) % 4 == 3 ) { echo ' viewleft'; }
				echo'">';
				echo"\n\t\t\t\t".'<span>';
				$textcontent = $val['content'];
				$textcontent = preg_replace('#\n#isU', ' ', $textcontent);
				$textcontent = preg_replace('#\t#isU', ' ', $textcontent);
				$textcontent = preg_replace('#\s\s+#isU', ' ', $textcontent);
				$textcontent = preg_replace('#,([a-zA-Z0-9])#isU', ', $1', $textcontent);
				$textcontent = preg_replace('#(\.|\?|!)([^h][^t][^m])#isU', '$1 '.strtoupper('$2'), $textcontent);
				$textcontent = preg_replace('#([a-zA-Z])<#isU', '$1 <', $textcontent);
				$textcontent = preg_replace('#^.*Explanation:#isU', '', $textcontent);
				$textcontent = preg_replace('#^ *</?[a-zA-Z0-9]+>#isU', '', $textcontent);
				$textcontent = preg_replace('#<hr>.*$#isU', '', $textcontent);
				$textcontent = strip_tags($textcontent);
				$textcontent = preg_replace('#(\.|\?|!)([^h][^t][^m])#isU', '$1<br />'.strtoupper('$2'), $textcontent);
				$textcontent = preg_replace('#<[^§]{,5}$#isU', '', $textcontent);
				$textcontent = htmlspecialchars($textcontent);
				$textcontent = str_replace('&lt;br /&gt;', '<br />', $textcontent);
				$textcontent = preg_replace('#([iI]t)[^a-z\']s#isU', '$1\'s', $textcontent);
				if ( strlen($textcontent) > 350 ) { $textcontent = substr($textcontent, 0, 350); }
				$textcontent = preg_replace('#<$#isU', '', $textcontent);
				$textcontent = preg_replace('#<b$#isU', '', $textcontent);
				$textcontent = preg_replace('#<br$#isU', '', $textcontent);
				$textcontent = preg_replace('#<br $#isU', '', $textcontent);
				$textcontent = preg_replace('#<br /$#isU', '', $textcontent);
				$textcontent = preg_replace('#<br />$#isU', '', $textcontent);
				if ( strlen($textcontent) >= 350 ) { $textcontent = $textcontent.' (...)'; }
				echo '<u>Extract :</u><br />'.$textcontent;
				echo'</span>'.$val['title'];



				/*---------------------Début de l'affichage des images---------------------*/
				if ( !$viewimgs ) {
					if ( preg_match('#apod\.nasa\.gov#isU',$val['img']) ) {
						if ( preg_match('#(\.jpg|\.jpeg)$#isU', $val['img']) )	{ $file = 'images/'.$val['id'].'.jpg'; }
						elseif	( preg_match('#\.png$#isU', $val['img']) )	{ $file = 'images/'.$val['id'].'.png'; }
						elseif	( preg_match('#\.gif$#isU', $val['img']) )	{ $file = 'images/'.$val['id'].'.gif'; }
						else { $file = 'cross.png'; }
						if ( file_exists($file) && $file != 'cross.png' ) { echo "\n\t\t\t\t".'<img src="'.$file.'" class="viewpic" alt="'.$val['title'].'" />'; }
						elseif ( $file != 'cross.png' ) {
							$finalimg = generatemyimg($val['id']);
							echo "\n\t\t\t\t".'<img src="'.$finalimg.'" class="viewpic" alt="'.$val['title'].'" />';
						}
						$brtag = "\n\t\t\t";
					}
					if ( preg_match('#youtube#isU', $val['img']) )			{ echo "\t\t\t\t".'<img src="vid-youtube.png" alt="Video" />'; }
					elseif ( preg_match('#vimeo#isU', $val['img']) )		{ echo "\t\t\t\t".'<img src="vid-vimeo.png" alt="Video" />'; }
					elseif ( preg_match('#dailymotion#isU', $val['img']) )	{ echo "\t\t\t\t".'<img src="vid-dailymotion.png" alt="Video" />'; }
				} // endif !$viewimgs
				echo'</a>'."\n";
				/*--------------------- Fin de l'affichage des images ---------------------*/






				/*---------------------Début tags---------------------*/
				if ( $tags == true && !$viewimgs) { echo '<br />'; }
				if ( $tags == true ) {
					$txtbgn = $brtag.'<img src="tags/';

					if ( preg_match('#earth#isU', $val['title']) )
						{ echo $txtbgn.'Earth.png" alt="Earth" title="Earth" class="tagimg" />'; }
					if ( preg_match('#mars#isU', $val['title']) )
						{ echo $txtbgn.'mars.png" alt="Mars" title="Mars" class="tagimg" />'; }
					if ( preg_match('#comet#isU', $val['title']) )
						{ echo $txtbgn.'comet.png" alt="Comet" title="Comet" class="tagimg" />'; }
					if ( preg_match('#earth(\'s)? *moon|eclipse.*moon|moon.*eclipse#isU', $val['title']) )
						{ echo $txtbgn.'moon.png" alt="Moon" title="Moon" class="tagimg" />'; }
					if ( preg_match('#ngc#isU', $val['title']) )
						{ echo $txtbgn.'ngc.png" alt="NGC (New General Catalogue)" title="NGC (New General Catalogue)" class="tagimg" />'; }
					if ( preg_match('#(a|A)stero(i|ï|.)d#isU', $val['title']) )
						{ echo $txtbgn.'asteroid.png" alt="Astero&iuml;d" title="Astero&iuml;d" class="tagimg" />'; }

				} // end if $tags
				/*--------------------- Fin tags ---------------------*/



				echo "\n\t\t\t".'</td>'."\n";
				if ( ($key+1) % 4 == 0 ) {
					echo "\t\t</tr>\n\t";
					if ( ($key+1) < count($donnees) ) { echo "\t<tr>\n"; }
				}
			} // ------------- END FOREACH -------------- //
			echo '</table> <!-- /table inside contentview -->'."\n";
		?>
	</div> <!-- /div contentview -->


</body>
</html>