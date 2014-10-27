<?php
include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

die('Not Yet Implemented');

$idService = isset($_POST['id_service']) ? $_POST['id_service'] : isset($_GET['id_service']) ? $_GET['id_service'] : 1;
$idBorne = isset($_POST['id_borne']) ? $_POST['id_borne'] : isset($_GET['id_borne']) ? $_GET['id_borne'] : 1;

$result = $dbh->prepare('SELECT INSERT_TICKET(:service, :borne) as id_ticket');
$result->execute(array(':service' => $idService, ':borne' => $idBorne));

echo json_encode($result);
?>
