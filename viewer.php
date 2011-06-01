<?php 
if (preg_match('#astro\.pierstoval\.com#isU', $_SERVER['HTTP_HOST'])) {
	// do nothing
} else {
	header("Status: 301 Moved Permanently");
	header('Location: http://astro.pierstoval.com/view.php');
}
$id = isset($_GET['id']) ? $_GET['id'] : '';

include('bdd.php');
if ($id) {
	if ($id == 'last') {
		$query = 'SELECT * FROM `astropix_articles` WHERE `artdate` = (SELECT MAX(`artdate`) FROM `astropix_articles`)';
	} else {
		$id = intval($id);
		$query = 'SELECT * FROM `astropix_articles` WHERE `id` = '.$id;
	}
	$maxvalue = reqex('SELECT MAX(`id`) FROM `astropix_articles`');
	$maxvalue = intval($maxvalue['MAX(`id`)']);
	$data = reqex($query);
	if ($data) {
		if ($id == 'last') { $id = $data['id']; }
		$views = $data['views'];
		$idpresent = false;
		if (isset($_COOKIE['astropiers'])) {
			if (preg_match('#,#isU', $_COOKIE['astropiers'])) {
				$cookie = explode(',', $_COOKIE['astropiers']);
			} else {
				$cookie[0] = $_COOKIE['astropiers'];
			}
			foreach($cookie as $key => $val) {
				if ($id == $val) {
					$idpresent = true;
				}
			}
		} else {
			$cookie = array();
			setcookie('astropiers', $id, time()+43200, null, null, false, true);
		}
		if ($idpresent === false) {
			$views ++;
			$key = isset($key) ? $key : 0;
			$cookie[$key + 1] = $id;
			$cookie = implode(',', $cookie);
			setcookie('astropiers', $cookie, time()+43200, null, null, false, true);
			$query = 'UPDATE `astropix_articles` SET `views` = '.$views.' WHERE `astropix_articles`.`id` = '.$id;
			reqex($query);
		}
	} // end if $data
} // end if $id

?><!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Astronomy Picture of the Day</title>
	<link href="styles.css" rel="styleSheet" type="text/css" />
	<script type="text/javascript" src="functions.js"></script>
	<script>
	function googleSectionalElementInit() {
		new google.translate.SectionalElement({
			sectionalNodeClassName: 'gtradsec',
			controlNodeClassName: 'gtradcon',
			background: 'transparent'
		}, 'google_sectional_element');
	}
	window.___gcfg = {lang: <?php
				$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				$language = $language{0}.$language{1};
				echo '\''.$language.'\''
			?>};
	(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
	</script>
	<script src="//translate.google.com/translate_a/element.js?cb=googleSectionalElementInit&amp;ug=section&amp;hl=fr"></script>
</head>

<body>
	<div id="contentview">
		<table id="menuview"><?php 
			if (!$id) {
				echo '<tr>
					<td class="divview">
						Merci de choisir un article. Redirection dans 5s...
						<script type="text/javascript">setTimeout("window.location.href = \'view.php\';", 5000);</script>
					</td>
				</tr>';
			} else {
				if ($data) {
					$id = $data['id'];
					$prev = reqex('SELECT * FROM `astropix_articles` WHERE `artdate` < \''.$data['artdate'].'\' ORDER BY `artdate` DESC LIMIT 0, 1');
					$prev = $prev ? $prev['id'] : 0;
					$next = reqex('SELECT * FROM `astropix_articles` WHERE `artdate` > \''.$data['artdate'].'\' ORDER BY `artdate` ASC LIMIT 0, 1');
					$next = $next ? $next['id'] : 0;
					echo "\t\t\t<tr>\n";
					echo "\t\t\t\t".'<th class="divview" colspan="3">';
					if ($next) { echo '<a href="viewer.php?id='.$next.'">&lt;&lt;&lt;</a>&nbsp;'; }
					echo htmlentities($data['title']);
					if ($prev) { echo '&nbsp;<a href="viewer.php?id='.$prev.'">&gt;&gt;&gt;</a>'; }
					echo "</th>\n";
					echo "\t\t\t</tr>\n";
		
					echo "\t\t\t<tr>\n";
					echo "\t\t\t\t".'<td class="divview" id="artimage" colspan="2">'."\n";
					echo "\t\t\t\t\t".'<a href="'.$data['img'].'" target="_blank">';
					if (file_exists('images/'.$data['id'].'.jpg')) {
						echo '<img src="images/'.$data['id'].'.jpg" alt="'.$data['title'].'" />';
					} elseif (file_exists('images/'.$data['id'].'.gif')) {
						echo '<img src="images/'.$data['id'].'.gif" alt="'.$data['title'].'" />';
					} elseif (file_exists('images/'.$data['id'].'.jpeg')) {
						echo '<img src="images/'.$data['id'].'.jpeg" alt="'.$data['title'].'" />';
					} elseif (file_exists('images/'.$data['id'].'.png')) {
						echo '<img src="images/'.$data['id'].'.png" alt="'.$data['title'].'" />';
					} elseif ( preg_match('#youtube#isU', $data['img']) )	{ echo "\t\t\t\t".'<img src="vid-youtube.png" alt="Video" />'; }
					elseif ( preg_match('#vimeo#isU', $data['img']) )		{ echo "\t\t\t\t".'<img src="vid-vimeo.png" alt="Video" />'; }
					elseif ( preg_match('#dailymotion#isU', $data['img']) )	{ echo "\t\t\t\t".'<img src="vid-dailymotion.png" alt="Video" />'; }
					echo "</a>\n";
					echo "\t\t\t\t</td>\n";

					echo "\t\t\t\t".'<td class="divview artcontent" lang="en" rowspan="4">'."\n";
					echo '(Translation by Google)<div class="gtradsec"><span class="gtradcon" id="tradcon"></span>';
					$data['content'] = str_replace('<br />', "", $data['content']);
					$data['content'] = str_replace('. ', ".<br />\n", $data['content']);
					$data['content'] = str_replace('! ', "!<br />\n", $data['content']);
					$data['content'] = str_replace('? ', "?<br />\n", $data['content']);
					$data['content'] = preg_replace('#<[^a/][^§]*>#isU', " ", $data['content']);
					$data['content'] = preg_replace('#</[^a][^§]*>#isU', " ", $data['content']);
					$data['content'] = preg_replace('#(<.[^§]*>)(a-zA-Z0-9)#isU', "$1 $2", $data['content']);
					$data['content'] = str_replace('<a ', '<a target="_blank" ', $data['content']);
					$data['content'] = preg_replace('#&[^a][^m][^p]#', '&amp;', $data['content']);
					while (preg_match('#(href="[^"]+)\s#', $data['content'])) {
						$data['content'] = preg_replace('#(href="[^"]+)\s#', '$1', $data['content']);
					}
					echo $data['content'];
					echo '</div><!--/gtradsec-->';
					echo "\t\t\t\t".'</td>'."\n";
					echo "\t\t\t</tr>\n";
	
					echo "\t\t\t<tr>\n";
					echo "\t\t\t\t".'<td class="divview artlink" colspan="2">'."\n";
					echo "\t\t\t\t\t".'<a href="'.htmlentities($data['url']).'" target="_blank">';
						echo htmlentities($data['url']).'</a><br />'."\n";
					setlocale(LC_ALL, null);
					$data['artdate'] = explode('-', $data['artdate']);
					date_default_timezone_set('Europe/Paris');
					$data['artdate'] = mktime(0, 0, 0, $data['artdate'][1], $data['artdate'][2], $data['artdate'][0]);
					$data['artdate'] = strftime('%A, %d %B %Y', $data['artdate']);
					$data['artdate'] = str_replace(
						array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
						array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'),
						$data['artdate']
					);
					$data['artdate'] = str_replace(
						array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
						array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'),
						$data['artdate']
					);
					echo "\t\t\t\t\t".ucfirst($data['artdate'])."\n";
					echo "\t\t\t\t\t".'<br />Vues : '.$views."\n";
					echo "\t\t\t\t".'</td>'."\n";
					echo "\t\t\t</tr>\n";

					echo "\t\t\t<tr>\n";
					echo "\t\t\t\t".'<td class="divview blockfb">'."\n";
					echo "\t\t\t\t\t".'<iframe src="http://www.facebook.com/plugins/like.php?href=';
						echo urlencode('http://astro.pierstoval.com/viewer.php?id='.$id).'" ';
						echo 'id="blockfb"></iframe>'."\n";
					echo "\t\t\t\t".'</td>'."\n";
					echo "\t\t\t\t".'<td class="divview blockgp">'."\n";
					echo "\t\t\t\t\t".'<div class="g-plusone" data-href="http://astro.pierstoval.com/viewer.php?id='.$id.'"></div>';
					echo "\t\t\t\t".'</td>'."\n";
					echo "\t\t\t</tr>\n";
					echo "\t\t\t<tr><td>&nbsp;</td></tr>\n";
				} else {
					echo '<tr>
						<td class="divview">
						Merci de choisir un article existant. Redirection dans 5s...
						<script type="text/javascript">setTimeout("window.location.href = \'view.php\';", 5000);</script>
						</td>
					</tr>';
				}
			}
			?>
			<tr>
				<td class="divview" colspan="3"><a href="view.php">Retour &agrave; la liste / Back to the list</a></td>
			</tr>
		</table>
	</div> <!-- /div contentview -->
</body>
</html>