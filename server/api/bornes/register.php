<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('token' => 0, 'type' => 'borne') );

        // Check if the Borne is registered or not
        if ( count( Borne::GetAll($bdd, 'token = :token', array(':token' => $params['token'])) ) > 0 ) {
            throw new Exception("Error Already Registered");
        }

        $borne = new Borne(); // Create a new Borne
        $borne->State = 1; // Auto open the borne
        $borne->NbDelivered = 0;
        $borne->Token = $params['token'];
        $borne->Type = $params['type'];

        $borne->save($bdd); // Save it
        http_response_code(200);
        echo $borne->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
