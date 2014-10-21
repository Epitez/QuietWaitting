<?php
    $host = "10.16.160.241";
    $dbname = "q";
    $user = "q_acces";
    $pass = "xxxkevin";

    try {
        $dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bdd = $dbh;
    } catch(PDOException $e) {
        die ('SQL connect : '.$e->getMessage());
    }
?>
