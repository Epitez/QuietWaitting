<?php
include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

$result = $dbh->query('CALL LISTER_TICKETS()');

echo json_encode($result->fetchAll(PDO::FETCH_CLASS));
?>
