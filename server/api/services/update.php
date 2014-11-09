<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('id' => 0, 'name' => ''), true, false );
        // false to ignore missing parameters

        // Fetch the service
        $service = Service::Get($bdd, $params['id']);

        $service = Sanitize::fill_attributes($service, $params);

        $service->save($bdd); // Save it

        http_response_code(200);
        echo $service->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
