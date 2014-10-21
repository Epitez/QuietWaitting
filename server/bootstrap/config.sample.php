<?php
	$host = "localhost";
	$dbname = "dbname";
	$user = "user";
	$pass = "password";

	try {
		$dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$bdd = $dbh;
	} catch(PDOException $e) {
		die ('SQL connect : '.$e->getMessage());
	}
?>
