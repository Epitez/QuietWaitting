<?php
    include($_SERVER['DOCUMENT_ROOT'].'/bootstrap/start.php');
    require 'PHPLinq.php';

    $borne1 = Borne::Get($bdd, 23);
    $borne2 = Borne::Get($bdd, 24);
    $guichet1 = Guichet::Get($bdd, 17);
    $guichet2 = Guichet::Get($bdd, 18);
    $service1 = Service::Get($bdd, 25);
    $service2 = Service::Get($bdd, 26);
    $service3 = Service::Get($bdd, 27);
    $ticket1 = Ticket::Get($bdd, 1);
    $ticket2 = Ticket::Get($bdd, 2);
    $ticket3 = Ticket::Get($bdd, 3);
    $ticket4 = Ticket::Get($bdd, 4);
    $ticket5 = Ticket::Get($bdd, 5);
    $ticket6 = Ticket::Get($bdd, 6);
    $ticket7 = Ticket::Get($bdd, 7);
    $ticket8 = Ticket::Get($bdd, 8);

    $tickets = $borne1->tickets($bdd);
    foreach ($tickets as $key => $ticket) {
        var_dump($ticket->id());
    }

    echo '<br>';
    echo '<br>';

    $tickets = $service1->tickets($bdd);
    foreach ($tickets as $key => $ticket) {
        var_dump($ticket->id());
    }

    echo '<br>';
    echo '<br>';

    $names = array("John", "Peter", "Joe", "Patrick", "Donald", "Eric");

    $result = from('$name')->in($names)
                ->where('$name => strlen($name) < 5')
                ->select('$name');

    print_r($result);

    // $borne1->save($bdd);
    // $borne2->save($bdd);
    //
    // $guichet1->name = 'Guichet 1';
    // $guichet1->save($bdd);
    // $guichet2->name = 'Guichet 2';
    // $guichet2->save($bdd);
    // $service1->name = 'Service 1';
    // $service1->save($bdd);
    // $service2->name = 'Service 2';
    // $service2->save($bdd);
    // $service3->name = 'Service 3';;
    // $service3->save($bdd);
    //
    // $ticket1 = $borne1->createTicket($bdd, $service1);
    // $ticket2 = $borne1->createTicket($bdd, $service2);
    // $ticket3 = $borne1->createTicket($bdd, $service3);
    // $ticket4 = $borne1->createTicket($bdd, $service2);
    // $ticket5 = $borne2->createTicket($bdd, $service3);
    // $ticket6 = $borne1->createTicket($bdd, $service1);
    // $ticket7 = $borne2->createTicket($bdd, $service1);
    // $ticket8 = $borne2->createTicket($bdd, $service2);
    //
    // $borne1->debug();
    // $borne2->debug();
    // $guichet1->debug();
    // $guichet2->debug();
    // $service1->debug();
    // $service2->debug();
    // $service3->debug();
    // $ticket1->debug();
    // $ticket2->debug();
    // $ticket3->debug();
    // $ticket4->debug();
    // $ticket5->debug();
    // $ticket6->debug();
    // $ticket7->debug();
    // $ticket8->debug();
?>
