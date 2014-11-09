<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('id' => 0, 'name' => 'Guichet', 'ouvert' => 1), true, false );
        // false to ignore missing parameters

        // Fetch the guichet
        $guichet = Guichet::Get($bdd, $params['id']);

        $guichet = Sanitize::fill_attributes($guichet, $params);

        $guichet->save($bdd); // Save it

        http_response_code(200);
        echo $guichet->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
