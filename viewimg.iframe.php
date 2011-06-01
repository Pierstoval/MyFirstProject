<?php
$imgid = isset($_GET['id'])?intval($_GET['id']):'';
$finalimg = '';
include('params.php');
$query = 'SELECT img FROM astropix_articles WHERE id = '.$imgid;
$req = $bdd->prepare($query);
try { $req->execute(); }
catch (Exception $e) { exit;}
$data = $req->fetch();
if ( !$data or !preg_match('#(\.jpg|\.jpeg|\.png|\.gif)$#isU', $data['img']) ) {
	$finalimg = 'cross.png';
}
else {
	if ( preg_match('#(\.jpg|\.jpeg)$#isU', $data['img']) ) { $img = imagecreatefromjpeg($data['img']); }
	elseif ( preg_match('#(\.png)$#isU', $data['img']) ) { $img = imagecreatefrompng($data['img']);  }
	elseif ( preg_match('#(\.gif)$#isU', $data['img']) ) { $img = imagecreatefromgif($data['img']);  }
	else { exit; }
	$max = 115;
	$x = imagesx($img);
	$y = imagesy($img);
	if( $x>$max or $y>$max ) {
		if( $x>$y ) { $nx = $max; $ny = $y/($x/$max); }
		else { $nx = $x/($y/$max); $ny = $max; }
	}
	$nimg = imagecreatetruecolor($nx,$ny);
	imagecopyresampled($nimg,$img,0,0,0,0,$nx,$ny,$x,$y);
	if		( preg_match('#(\.jpg|\.jpeg)$#isU', $data['img']) ){ imagejpeg($nimg,'images/'.$imgid.'.jpg'); $finalimg = 'images/'.$imgid.'.jpg'; }
	elseif	( preg_match('#(\.png)$#isU', $data['img']) )		{ imagepng($nimg, 'images/'.$imgid.'.png'); $finalimg = 'images/'.$imgid.'.png'; }
	elseif	( preg_match('#(\.gif)$#isU', $data['img']) )		{ imagegif($nimg, 'images/'.$imgid.'.gif'); $finalimg = 'images/'.$imgid.'.gif'; }
	else	{ $finalimg = 'cross.png'; }
	echo '<img src="'.$finalimg.'" class="iframe" />';
}
?>