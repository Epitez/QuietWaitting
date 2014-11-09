<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('id' => 0, 'name' => 'Borne', 'ouvert' => 1), true, false );
        // false to ignore missing parameters

        // Fetch the borne
        $borne = Borne::Get($bdd, $params['id']);

        $borne = Sanitize::fill_attributes($borne, $params);

        $borne->save($bdd); // Save it

        http_response_code(200);
        echo $borne->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
