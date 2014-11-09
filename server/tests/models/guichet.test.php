<?php
    class GuichetTest extends PHPUnit_Framework_TestCase
    {
        public static $GuichetId = 0;
        public static $GuichetId2 = 0;

        public function testNewGuichet() {
            global $bdd;
            $guichet = new Guichet();
            $this->assertInstanceOf('Guichet', $guichet);
            $this->assertObjectHasAttribute('_id', $guichet);
            $this->assertObjectHasAttribute('Name', $guichet);
            $this->assertObjectHasAttribute('Ouvert', $guichet);
        }

        public function testSaveGuichet() {
            global $bdd;
            $guichet = new Guichet();
            $guichet->save($bdd);
            $this->assertGreaterThan(0, $guichet->id());
            $this->assertEquals('', $guichet->Name);
            $this->assertEquals(0, $guichet->Ouvert);

            GuichetTest::$GuichetId = $guichet->id();
        }

        /**
        * @depends testSaveGuichet
        */
        public function testGetGuichet() {
            global $bdd;
            $guichet = Guichet::Get($bdd, GuichetTest::$GuichetId);
            $this->assertEquals(GuichetTest::$GuichetId, $guichet->id());
            $this->assertEquals('', $guichet->Name);
            $this->assertEquals(0, $guichet->Ouvert);
        }

        /**
        * @depends testGetGuichet
        */
        public function testUpdateGuichet() {
            global $bdd;
            $guichet = Guichet::Get($bdd, GuichetTest::$GuichetId);
            $guichet->Name='GuichetTest';
            $guichet->Ouvert=1;
            $guichet->save($bdd);
            unset($guichet);
            $guichet = Guichet::Get($bdd, GuichetTest::$GuichetId);
            $this->assertEquals(GuichetTest::$GuichetId, $guichet->id());
            $this->assertEquals('GuichetTest', $guichet->Name);
            $this->assertEquals(1, $guichet->Ouvert);
        }

        /**
        * @depends testUpdateGuichet
        */
        public function testGetALLGuichet() {
            global $bdd;
            $guichet1 = new Guichet();
            $guichet1->save($bdd);
            GuichetTest::$GuichetId2 = $guichet1->id();
            $guichet2 = Guichet::Get($bdd, GuichetTest::$GuichetId);
            $guichets = Guichet::GetAll($bdd);
            $g1Found = false;
            $g2Found = false;
            foreach ($guichets as $key => $guichet) {
                if ($guichet->id() == $guichet1->id()) {
                    $this->assertEquals($guichet1, $guichet);
                    $g1Found = true;
                } elseif ($guichet->id() == $guichet2->id()) {
                    $this->assertEquals($guichet2, $guichet);
                    $g2Found = true;
                }
            }
            $this->assertTrue($g1Found);
            $this->assertTrue($g2Found);
        }

        /**
        * @depends testGetGuichet
        * @depends testUpdateGuichet
        * @depends testGetALLGuichet
        * @expectedException NotFoundException
        */
        public function testDestroyGuichet() {
            global $bdd;
            $guichet = Guichet::Get($bdd, GuichetTest::$GuichetId);
            $guichet->destroy($bdd);
            $this->assertNull($guichet->id());
            Guichet::Get($bdd, GuichetTest::$GuichetId2)->destroy($bdd);
            Guichet::Get($bdd, GuichetTest::$GuichetId);
        }
    }
?>
