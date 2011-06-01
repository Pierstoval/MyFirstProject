<?php

if ( $_SERVER['HTTP_HOST'] == '127.0.0.1') {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$dbname = 'astropix';
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpwd = '';
}
else {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$dbname = 'astropiers';
	$dbhost = 'localhost';
	$dbuser = 'astropiers';
	$dbpwd = '';
}


$dsn = 'mysql:host='.$dbhost.';dbname='.$dbname;

$bdd = new PDO($dsn, $dbuser, $dbpwd, $pdo_options);

function reqex ($query) {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	if ( $_SERVER['HTTP_HOST'] == '127.0.0.1') {
		$dbname = 'astropix';
		$dbhost = 'localhost';
		$dbuser = 'root';
		$dbpwd = '';
	}
	else {
	        $dbname = 'astropiers';
	        $dbhost = 'localhost';
	        $dbuser = 'astropiers';
	        $dbpwd = '';
	}
	$dsn = 'mysql:host='.$dbhost.';dbname='.$dbname;
	$bdd = new PDO($dsn, $dbuser, $dbpwd, $pdo_options);

	$req = $bdd->prepare($query);
	if (preg_match('#SELECT #isU', $query)) {
		try { $req->execute(); }
		catch (Exception $e) { return false;}
		$data = $req->fetchAll();
		if (isset($data[0])) { $data = $data[0]; }
		$max = count($data);
		for ($i = 0; $i < $max; $i++) { unset($data[$i]); }
		return $data;
	} else {
		try { $req->execute(); return true; }
		catch (Exception $e) { return false; }
	}
}

reqex('SET NAMES "UTF8"');

?>
