<?php
include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

$idGuichet = isset($_POST['id_guichet']) ? $_POST['id_guichet'] : isset($_GET['id_guichet']) ? $_GET['id_guichet'] : 1;

$result = $dbh->prepare('CALL CLOSE_GUICHET(:guichet)');
$result->execute(array(':guichet' => $idGuichet));

if ($result) {
	http_response_code(200);
} else {
	http_response_code(401);
}
?>
