<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        Ticket::Reset();
        header('Location: /api/call/state.php');
        exit();
    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
