<?php

error_reporting(E_ALL);

require_once __DIR__ . '/Common.php';

class RepoTest extends Common
{
    private $testIp = '1.2.3.4';

    public function testAddIp()
    {
        $this->repo->addIp($this->testIp, $this->cfg->getBlacklistTable());

        $row = $this->mapper->findOne($this->cfg->getBlacklistTable(), []);

        $dateTimeRegex = '~^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$~';

        $this->assertEquals($this->testIp, $row['ip']);
        $this->assertRegExp($dateTimeRegex, $row['dateTime']);
    }

    public function testIsIpLogged()
    {
        // seed data
        $this->repo->addIp($this->testIp, $this->cfg->getBlacklistTable());

        $logged = $this->repo->isIpLogged($this->testIp, $this->cfg->getBlacklistTable());

        $this->assertTrue($logged);
    }
}
