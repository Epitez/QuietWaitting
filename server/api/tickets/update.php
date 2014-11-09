<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        $time = date( 'Y-m-d H:i:s', Time() );
        $params = Sanitize::get_params( array('id' => 0, 'state' => '', 'ferme' => NULL, 'absent' => 0), true, false );
        // false to ignore missing parameters

        // Fetch the ticket
        $ticket = Ticket::Get($bdd, $params['id']);

        $ticket = Sanitize::fill_attributes($ticket, $params);

        $ticket->save($bdd); // Save it

        http_response_code(200);
        echo $ticket->to_json(); // Respond with the object

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
