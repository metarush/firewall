<?php

error_reporting(E_ALL);

require_once __DIR__ . '/Common.php';

class RepoTest extends Common
{
    private $testIp = '1.2.3.4';

    public function testAddToBlacklist()
    {
        $lastInsertId = $this->repo->addToBlacklist($this->testIp);

        $row = $this->mapper->findOne($this->cfg->getBlacklistTable(), ['id' => 1]);

        $dateTimeRegex = '~^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$~';

        $this->assertEquals(1, $lastInsertId);
        $this->assertEquals($this->testIp, $row['ip']);
        $this->assertRegExp($dateTimeRegex, $row['dateTime']);
    }

    public function testAddToWhitelist()
    {
        $lastInsertId = $this->repo->addToWhitelist($this->testIp);

        $row = $this->mapper->findOne($this->cfg->getWhitelistTable(), ['id' => 1]);

        $dateTimeRegex = '~^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$~';

        $this->assertEquals(1, $lastInsertId);
        $this->assertEquals($this->testIp, $row['ip']);
        $this->assertRegExp($dateTimeRegex, $row['dateTime']);
    }

    public function testIsBlacklisted()
    {
        // seed data
        $this->repo->addToBlacklist($this->testIp);

        $blacklisted = $this->repo->isBlacklisted($this->testIp);

        $this->assertTrue($blacklisted);
    }
}
