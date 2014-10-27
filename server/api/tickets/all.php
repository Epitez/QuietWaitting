<?php
include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

//$result = $dbh->query('CALL LISTER_TICKETS()');
$result = Ticket::GetAll($dbh);

echo json_encode($result);
?>
