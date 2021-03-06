<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        // Fetch all guichet
        $guichets = Guichet::GetAll($bdd);

        http_response_code(200);
        // Build response JSON
        $json = '';
        $begin = '[';
        $obj_separator = ', ';
        $end = ']';
        foreach ($guichets as $key => $guichet) {
            $json .= $guichet->to_json().$obj_separator;
        }
        $json = trim($json, ", "); // remove the trailing ', '.
        echo $begin.$json.$end; // Send the JSON

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
