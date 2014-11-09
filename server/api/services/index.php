<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        // Fetch all service
        $services = Service::GetAll($bdd);

        http_response_code(200);
        // Build response JSON
        $json = '';
        $begin = '[';
        $obj_separator = ', ';
        $end = ']';
        foreach ($services as $key => $service) {
            $json .= $service->to_json().$obj_separator;
        }
        $json = trim($json, ", "); // remove the trailing ', '.
        echo $begin.$json.$end; // Send the JSON

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
