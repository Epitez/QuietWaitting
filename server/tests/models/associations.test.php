<?php
    class AssociationsTest extends PHPUnit_Framework_TestCase
    {
        public static $TicketId = 0;
        public static $TicketId2 = 0;
        public static $TicketId3 = 0;
        public static $BorneId = 0;
        public static $ServiceId = 0;
        public static $GuichetId = 0;

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
        public function testSaveGuichet() {
            global $bdd;
            $guichet = new Guichet();
            $guichet->save($bdd);
            $this->assertGreaterThan(0, $guichet->id());
            $this->assertEquals('', $guichet->Name);
            $this->assertEquals(0, $guichet->Ouvert);

            static::$GuichetId = $guichet->id();
        }

        /**
        * @depends testSaveBorne
        * @depends testSaveService
        * @depends testSaveGuichet
        */
        public function testSaveTickets() {
            global $bdd;
            Ticket::initialize($bdd);
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

            static::$TicketId = $ticket->id();

            $ticket2 = new Ticket();
            $ticket2->IdBorne = static::$BorneId;
            $ticket2->IdService = static::$ServiceId;
            $ticket2->save($bdd);

            static::$TicketId2 = $ticket2->id();
        }

        /**
         * @depends testSaveTickets
         */
        public function testGuichetAssociations() {
            global $bdd;
            $service = Service::Get($bdd, static::$ServiceId);
            $guichet = Guichet::Get($bdd, static::$GuichetId);

            $guichet->addService($bdd, $service);
            $this->assertEquals($service, $guichet->addService($bdd, $service)); // won't work if $service load $_assoc
            $this->assertWithinArray($service, $guichet->services($bdd));
            $this->assertWithinArray($guichet, $service->guichets($bdd));

            $guichet->removeService($bdd, $service);
            $this->assertWithinArray($service, $guichet->services($bdd), true);
            $this->assertWithinArray($guichet, $service->guichets($bdd), true);
            $this->assertNull($guichet->removeService($bdd, $service));
        }

        /**
         * @depends testSaveTickets
         */
        public function testServiceAssociations() {
            global $bdd;
            $service = Service::Get($bdd, static::$ServiceId);
            $guichet = Guichet::Get($bdd, static::$GuichetId);
            $ticket1 = Ticket::Get($bdd, static::$TicketId);
            $ticket2 = Ticket::Get($bdd, static::$TicketId2);

            $service->addGuichet($bdd, $guichet);
            $this->assertEquals($guichet, $service->addGuichet($bdd, $guichet)); // won't work if $guichet load $_assoc
            $this->assertWithinArray($service, $guichet->services($bdd));
            $this->assertWithinArray($guichet, $service->guichets($bdd));

            $service->removeGuichet($bdd, $guichet);
            $this->assertWithinArray($service, $guichet->services($bdd), true);
            $this->assertWithinArray($guichet, $service->guichets($bdd), true);
            $this->assertNull($service->removeGuichet($bdd, $guichet));

            $this->assertWithinArray($ticket1, $service->tickets($bdd));
            $this->assertWithinArray($ticket2, $service->tickets($bdd));

            $this->assertEquals($service->id(), $ticket1->service($bdd)->id());
            $this->assertEquals($service->id(), $ticket2->service($bdd)->id());
        }

        /**
         * @depends testSaveTickets
         */
        public function testTicketAssociations() {
            global $bdd;
            $ticket1 = Ticket::Get($bdd, static::$TicketId);
            $ticket2 = Ticket::Get($bdd, static::$TicketId2);
            $borne = Borne::Get($bdd, static::$BorneId);
            $service = Service::Get($bdd, static::$ServiceId);

            $this->assertEquals($borne->id(), $ticket1->borne($bdd)->id());
            $this->assertEquals($borne->id(), $ticket2->borne($bdd)->id());

            $this->assertWithinArray($ticket1, $borne->tickets($bdd));
            $this->assertWithinArray($ticket2, $borne->tickets($bdd));

            $this->assertWithinArray($ticket1, $service->tickets($bdd));
            $this->assertWithinArray($ticket2, $service->tickets($bdd));

            $this->assertEquals($service->id(), $ticket1->service($bdd)->id());
            $this->assertEquals($service->id(), $ticket2->service($bdd)->id());

            $session = Ticket::CurrentSession();
            Ticket::Reset();
            $this->assertEquals(1, Ticket::NextDistributed());
            $this->assertEquals($session + 1, Ticket::CurrentSession());
        }

        /**
         * @depends testSaveTickets
         * @depends testTicketAssociations
         */
         public function testBorneAssociations() {
             global $bdd;
             $ticket1 = Ticket::Get($bdd, static::$TicketId);
             $ticket2 = Ticket::Get($bdd, static::$TicketId2);
             $service = Service::Get($bdd, static::$ServiceId);
             $borne = Borne::Get($bdd, static::$BorneId);
             $ticket3 = $borne->createTicket($bdd, $service);
             static::$TicketId3 = $ticket3->id();

            $this->assertEquals($service->id(), $ticket3->service($bdd)->id());
            $this->assertWithinArray($ticket3, $service->tickets($bdd));

             $this->assertEquals($borne->id(), $ticket1->borne($bdd)->id());
             $this->assertEquals($borne->id(), $ticket2->borne($bdd)->id());
             $this->assertEquals($borne->id(), $ticket3->borne($bdd)->id());

             $this->assertWithinArray($ticket1, $borne->tickets($bdd));
             $this->assertWithinArray($ticket2, $borne->tickets($bdd));
             $this->assertWithinArray($ticket3, $borne->tickets($bdd));
         }

        private function assertWithinArray($needle, $haystack, $inverse=false) {
            $needleFound = false;
            foreach ($haystack as $key => $value) {
                if ($value->id() == $needle->id()) {
                    $needleFound = true;
                    break;
                }
            }
            $this->assertTrue($needleFound xor $inverse); // this will inverse the condition if $inverse is true
        }

        /**
        * @depends testGuichetAssociations
        * @depends testServiceAssociations
        * @depends testTicketAssociations
        * @depends testBorneAssociations
        * @expectedException NotFoundException
        */
        public function testDestroyTicket() {
            global $bdd;
            $ticket = Ticket::Get($bdd, TicketTest::$TicketId);
            $ticket->destroy($bdd);
            $this->assertNull($ticket->id());
            Ticket::Get($bdd, static::$TicketId2)->destroy($bdd);
            Ticket::Get($bdd, static::$TicketId3)->destroy($bdd);
            Borne::Get($bdd, static::$BorneId)->destroy($bdd);
            Service::Get($bdd, static::$ServiceId)->destroy($bdd);
            Guichet::Get($bdd, static::$GuichetId)->destroy($bdd);
            Ticket::Get($bdd, static::$TicketId);
        }
    }
?>
