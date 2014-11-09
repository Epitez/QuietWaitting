<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('token' => 0, 'type' => 'Borne'));

        // Check if the Borne is registered or not
        if ( count( Borne::GetAll($bdd, 'token = :token', array(':token' => $params['token'])) ) > 0 ) {
            throw new Exception("Error Already Registered");
        }

        // Instantiate a new borne
        $borne = new Borne();

        $borne = Sanitize::fill_attributes($borne, $params);

        $borne->save($bdd); // Save it (Creation)

        http_response_code(200);
        echo $borne->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
