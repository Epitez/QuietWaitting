<?php

    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');

    try {
        // Fetch the guichet
        $current = Ticket::CurrentCall();
        $next = $current + 1;
        $last_distributed = Ticket::Last();
        $session = Ticket::CurrentSession();

        http_response_code(200);
        echo '{current: '.$current.', next: '.$next.', last_distributed: '.$last_distributed.', session: '.$session.'}';

    } catch (Exception $e) { // Something went wrong
        http_response_code(500);
        echo '{ error: true, errorMessage: "'.$e->getMessage().'" }';
    }

?>
