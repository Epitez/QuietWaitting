<?php
    class BorneTest extends PHPUnit_Framework_TestCase
    {
        public static $BorneId = 0;

        public function testNewBorne() {
            global $bdd;
            $borne = new Borne();
            $this->assertInstanceOf('Borne', $borne);
            $this->assertObjectHasAttribute('_id', $borne);
            $this->assertObjectHasAttribute('Token', $borne);
            $this->assertObjectHasAttribute('Type', $borne);
            $this->assertObjectHasAttribute('State', $borne);
            $this->assertObjectHasAttribute('NbDelivered', $borne);
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

            BorneTest::$BorneId = $borne->id();
        }

        /**
        * @depends testSaveBorne
        */
        public function testGetBorne() {
            global $bdd;
            $borne = Borne::Get($bdd, BorneTest::$BorneId);
            $this->assertEquals(BorneTest::$BorneId, $borne->id());
            $this->assertEquals(1, $borne->State);
            $this->assertEquals(0, $borne->Token);
            $this->assertEquals(0, $borne->NbDelivered);
            $this->assertEquals('Borne', $borne->Type);
        }

        /**
        * @depends testGetBorne
        */
        public function testUpdateBorne() {
            global $bdd;
            $borne = Borne::Get($bdd, BorneTest::$BorneId);
            $borne->State=0;
            $borne->Token=12345;
            $borne->NbDelivered=10000;
            $borne->Type='SP';
            $borne->save($bdd);
            unset($borne);
            $borne = Borne::Get($bdd, BorneTest::$BorneId);
            $this->assertEquals(BorneTest::$BorneId, $borne->id());
            $this->assertEquals(0, $borne->State);
            $this->assertEquals(12345, $borne->Token);
            $this->assertEquals(10000, $borne->NbDelivered);
            $this->assertEquals('SP', $borne->Type);
        }

        /**
        * @depends testUpdateBorne
        */
        public function testGetALLBorne() {
            global $bdd;
            $borne1 = new Borne();
            $borne1->save($bdd);
            $borne2 = Borne::Get($bdd, BorneTest::$BorneId);
            $bornes = Borne::GetAll($bdd);
            $b1Found = false;
            $b2Found = false;
            foreach ($bornes as $key => $borne) {
                if ($borne->id() == $borne1->id()) {
                    $this->assertEquals($borne1, $borne);
                    $b1Found = true;
                } elseif ($borne->id() == $borne2->id()) {
                    $this->assertEquals($borne2, $borne);
                    $b2Found = true;
                }
            }
            $this->assertTrue($b1Found);
            $this->assertTrue($b2Found);
        }

        /**
        * @depends testGetBorne
        * @depends testUpdateBorne
        * @depends testGetALLBorne
        * @expectedException NotFoundException
        */
        public function testDestroyBorne() {
            global $bdd;
            $borne = Borne::Get($bdd, BorneTest::$BorneId);
            $borne->destroy($bdd);
            $this->assertNull($borne->id());
            Borne::Get($bdd, BorneTest::$BorneId);
        }
    }
?>
