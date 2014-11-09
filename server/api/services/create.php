<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('name' => ''), true, false );
        // false to ignore missing parameters

        // Instantiate a new service
        $service = new Service();

        $service = Sanitize::fill_attributes($service, $params);

        $service->save($bdd); // Save it (Creation)

        http_response_code(200);
        echo $service->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
