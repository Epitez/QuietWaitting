<?php
    class TicketTest extends PHPUnit_Framework_TestCase
    {
        public static $TicketId = 0;
        public static $TicketId2 = 0;
        public static $BorneId = 0;
        public static $ServiceId = 0;

        public function testNewTicket() {
            global $bdd;
            $ticket = new Ticket();
            $this->assertInstanceOf('Ticket', $ticket);
            $this->assertObjectHasAttribute('_id', $ticket);
            $this->assertObjectHasAttribute('Number', $ticket);
            $this->assertObjectHasAttribute('Session', $ticket);
            $this->assertObjectHasAttribute('State', $ticket);
            $this->assertObjectHasAttribute('Ouvert', $ticket);
            $this->assertObjectHasAttribute('Absent', $ticket);
            $this->assertObjectHasAttribute('Ferme', $ticket);
            $this->assertObjectHasAttribute('IdBorne', $ticket);
            $this->assertObjectHasAttribute('IdService', $ticket);
        }

        public function testSaveBorne() {
            global $bdd;
            $borne = new Borne();
            $borne->save($bdd);
            $this->assertGreaterThan(0, $borne->id());
            $this->assertEquals(1, $borne->State);
            $this->assertEquals(0, $borne->Token);
            $this->assertEquals(0, $borne->NbDelivered);
            $this->assertEquals('Borne', $borne->Type);

            static::$BorneId = $borne->id();
        }
        public function testSaveService() {
            global $bdd;
            $service = new Service();
            $service->save($bdd);
            $this->assertGreaterThan(0, $service->id());
            $this->assertEquals('', $service->Name);

            static::$ServiceId = $service->id();
        }

        /**
        * @depends testSaveBorne
        * @depends testSaveService
        */
        public function testSaveTicket() {
            global $bdd;
            $ticket = new Ticket();
            $ticket->IdBorne = static::$BorneId;
            $ticket->IdService = static::$ServiceId;
            $ticket->save($bdd);
            $this->assertGreaterThan(0, $ticket->id());
            $this->assertEquals(Ticket::Last(), $ticket->Number);
            $this->assertEquals(Ticket::CurrentSession(), $ticket->Session);
            $this->assertEquals('en cours', $ticket->State);
            $this->assertEquals(0, $ticket->Absent);
            $this->assertNull($ticket->Ferme);
            $this->assertEquals(static::$BorneId, $ticket->IdBorne);
            $this->assertEquals(static::$ServiceId, $ticket->IdService);

            TicketTest::$TicketId = $ticket->id();
        }

        /**
        * @depends testSaveTicket
        */
        public function testGetTicket() {
            global $bdd;
            $ticket = Ticket::Get($bdd, TicketTest::$TicketId);
            $this->assertEquals(TicketTest::$TicketId, $ticket->id());
            $this->assertEquals(Ticket::Last(), $ticket->Number);
            $this->assertEquals(Ticket::CurrentSession(), $ticket->Session);
            $this->assertEquals('en cours', $ticket->State);
            $this->assertEquals(0, $ticket->Absent);
            $this->assertNull($ticket->Ferme);
            $this->assertEquals(static::$BorneId, $ticket->IdBorne);
            $this->assertEquals(static::$ServiceId, $ticket->IdService);
        }

        /**
        * @depends testGetTicket
        */
        public function testUpdateTicket() {
            global $bdd;
            date_default_timezone_set('Europe/Paris');
            $ticket = Ticket::Get($bdd, TicketTest::$TicketId);
            $ticket->Number = 12;
            $ticket->Session = 0;
            $ticket->State = 'traite';
            $ticket->Absent = 1;
            $time = date( 'Y-m-d H:i:s', Time() );
            $ticket->Ferme = $time;
            $ticket->save($bdd);
            unset($ticket);
            $ticket = Ticket::Get($bdd, TicketTest::$TicketId);
            $this->assertEquals(TicketTest::$TicketId, $ticket->id());
            $this->assertEquals(12, $ticket->Number);
            $this->assertEquals(0, $ticket->Session);
            $this->assertEquals('traite', $ticket->State);
            $this->assertEquals(1, $ticket->Absent);
            $this->assertEquals($time, $ticket->Ferme);
            $this->assertEquals(static::$BorneId, $ticket->IdBorne);
            $this->assertEquals(static::$ServiceId, $ticket->IdService);
        }

        /**
        * @depends testUpdateTicket
        */
        public function testGetALLTicket() {
            global $bdd;
            $ticket1 = new Ticket();
            $ticket1->IdBorne = static::$BorneId;
            $ticket1->IdService = static::$ServiceId;
            $ticket1->save($bdd);
            TicketTest::$TicketId2 = $ticket1->id();
            $ticket1 = Ticket::Get($bdd, TicketTest::$TicketId2);
            $ticket2 = Ticket::Get($bdd, TicketTest::$TicketId);
            $tickets = Ticket::GetAll($bdd);
            $t1Found = false;
            $t2Found = false;
            foreach ($tickets as $key => $ticket) {
                if ($ticket->id() == $ticket1->id()) {
                    $this->assertEquals($ticket1, $ticket);
                    $t1Found = true;
                } elseif ($ticket->id() == $ticket2->id()) {
                    $this->assertEquals($ticket2, $ticket);
                    $t2Found = true;
                }
            }
            $this->assertTrue($t1Found);
            $this->assertTrue($t2Found);
        }

        /**
        * @depends testGetTicket
        * @depends testUpdateTicket
        * @depends testGetALLTicket
        * @expectedException NotFoundException
        */
        public function testDestroyTicket() {
            global $bdd;
            $ticket = Ticket::Get($bdd, TicketTest::$TicketId);
            $ticket->destroy($bdd);
            $this->assertNull($ticket->id());
            Ticket::Get($bdd, TicketTest::$TicketId2)->destroy($bdd);
            Borne::Get($bdd, TicketTest::$BorneId)->destroy($bdd);
            Service::Get($bdd, TicketTest::$ServiceId)->destroy($bdd);
            Ticket::Get($bdd, TicketTest::$TicketId);
        }
    }
?>
