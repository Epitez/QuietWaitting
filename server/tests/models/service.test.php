<?php
    class ServiceTest extends PHPUnit_Framework_TestCase
    {
        public static $ServiceId = 0;
        public static $ServiceId2 = 0;

        public function testNewService() {
            global $bdd;
            $service = new Service();
            $this->assertInstanceOf('Service', $service);
            $this->assertObjectHasAttribute('_id', $service);
            $this->assertObjectHasAttribute('Name', $service);
        }

        public function testSaveService() {
            global $bdd;
            $service = new Service();
            $service->save($bdd);
            $this->assertGreaterThan(0, $service->id());
            $this->assertEquals('', $service->Name);

            ServiceTest::$ServiceId = $service->id();
        }

        /**
        * @depends testSaveService
        */
        public function testGetService() {
            global $bdd;
            $service = Service::Get($bdd, ServiceTest::$ServiceId);
            $this->assertEquals(ServiceTest::$ServiceId, $service->id());
            $this->assertEquals('', $service->Name);
        }

        /**
        * @depends testGetService
        */
        public function testUpdateService() {
            global $bdd;
            $service = Service::Get($bdd, ServiceTest::$ServiceId);
            $service->Name='TestGuichet';
            $service->save($bdd);
            unset($service);
            $service = Service::Get($bdd, ServiceTest::$ServiceId);
            $this->assertEquals(ServiceTest::$ServiceId, $service->id());
            $this->assertEquals('TestGuichet', $service->Name);
        }

        /**
        * @depends testUpdateService
        */
        public function testGetALLService() {
            global $bdd;
            $service1 = new Service();
            $service1->save($bdd);
            ServiceTest::$ServiceId2 = $service1->id();
            $service2 = Service::Get($bdd, ServiceTest::$ServiceId);
            $services = Service::GetAll($bdd);
            $s1Found = false;
            $s2Found = false;
            foreach ($services as $key => $service) {
                if ($service->id() == $service1->id()) {
                    $this->assertEquals($service1, $service);
                    $s1Found = true;
                } elseif ($service->id() == $service2->id()) {
                    $this->assertEquals($service2, $service);
                    $s2Found = true;
                }
            }
            $this->assertTrue($s1Found);
            $this->assertTrue($s2Found);
        }

        /**
        * @depends testGetService
        * @depends testUpdateService
        * @depends testGetALLService
        * @expectedException NotFoundException
        */
        public function testDestroyService() {
            global $bdd;
            $service = Service::Get($bdd, ServiceTest::$ServiceId);
            $service->destroy($bdd);
            $this->assertNull($service->id());
            Service::Get($bdd, ServiceTest::$ServiceId2)->destroy($bdd);
            Service::Get($bdd, ServiceTest::$ServiceId);
        }
    }
?>
