<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('id' => 0, 'id_service' => 0) );

        // Fetch the guichet
        $guichet = Guichet::Get($bdd, $params['id']);

        // Fetch the service
        $service = Service::Get($bdd, $params['id']);

        $guichet->removeService($bdd, $service);

        http_response_code(200);
        echo $service->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
