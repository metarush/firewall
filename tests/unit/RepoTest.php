<?php

error_reporting(E_ALL);

require_once __DIR__ . '/Common.php';

class RepoTest extends Common
{
    private $testIp = '1.2.3.4';
    private $testTable = 'blacklist';

    public function testAddIp()
    {
        $this->repo->addIp($this->testIp, $this->testTable);

        $row = $this->mapper->findOne($this->testTable, []);
        $this->assertEquals($this->testIp, $row['ip']);

        $dateTimeRegex = '~^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$~';
        $this->assertRegExp($dateTimeRegex, $row['dateTime']);
    }

    public function testAddIpAllowDuplicate()
    {
        for ($i = 0; $i < 3; $i++)
            $this->repo->addIp($this->testIp, $this->testTable, true);

        $rows = $this->mapper->findAll($this->testTable, ['ip' => $this->testIp]);
        $this->assertCount(3, $rows);
    }

    public function testIsIpLogged()
    {
        // seed data
        $this->repo->addIp($this->testIp, $this->testTable);

        $logged = $this->repo->isIpLogged($this->testIp, $this->testTable);

        $this->assertTrue($logged);
    }

    public function testCountIp()
    {
        for ($i = 0; $i < 5; $i++)
            $this->repo->addIp($this->testIp, $this->testTable, true);

        $count = $this->repo->countIp($this->testIp, $this->testTable);

        $this->assertEquals(5, $count);
    }
}
