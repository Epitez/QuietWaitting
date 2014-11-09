<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $params = Sanitize::get_params( array('id_borne' => 0, 'id_service' => 0), true, true );
        // true to force these parameters

        // Instantiate a new ticket
        $ticket = new Ticket();

        $ticket = Sanitize::fill_attributes($ticket, $params);

        $ticket->save($bdd); // Save it (Creation)

        http_response_code(200);
        echo $ticket->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
