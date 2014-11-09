<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('id' => 0) );

        // Fetch the Borne
        $borne = Borne::Get($bdd, $params['id']);

        $borne->State = 1; // Open the borne

        $borne->save($bdd); // Save it

        http_response_code(200);
        echo $borne->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
