<?php

	include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

	try {
		$params = Sanitize::get_params( array('id' => 0) );

		// Fetch the guichet
		$guichet = Guichet::Get($bdd, $params['id']);

		$guichet->Ouvert = 0; // Close the guichet

		$guichet->save($bdd); // Save it

		http_response_code(200);
		echo $guichet->to_json(); // Respond with the object

	} catch (Exception $e) { // Something went wrong
		http_response_code(500);
		echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
	}

?>
