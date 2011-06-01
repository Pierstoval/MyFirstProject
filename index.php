<?php 
if (preg_match('#astro\.pierstoval\.com#isU', $_SERVER['HTTP_HOST'])) {
	header('Location: view.php');
} else {
	header("Status: 301 Moved Permanently");
	header('Location: http://astro.pierstoval.com/view.php');
}
?>