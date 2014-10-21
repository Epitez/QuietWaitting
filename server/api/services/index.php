<?php
include($_SERVER['DOCUMENT_ROOT'].'/db/conf.php');

$result = $dbh->query('CALL LISTER_SERVICES()');

echo json_encode($result->fetchAll(PDO::FETCH_CLASS));
?>