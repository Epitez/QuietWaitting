<?php
include($_SERVER['DOCUMENT_ROOT'].'/db/conf.php');

$result = $dbh->query('CALL LISTER_TICKETS()');

echo json_encode($result->fetchAll(PDO::FETCH_CLASS));
?>