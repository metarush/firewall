<?php

error_reporting(E_ALL);

require_once __DIR__ . '/Common.php';

class FirewallTest extends Common
{

    public function testAddToBlacklist()
    {
        $ip = '1.2.3.4';

        $lastInsertId = $this->firewall->addToBlacklist($ip);

        $row = $this->mapper->findOne($this->cfg->getBlacklistTable(), ['id' => 1]);

        $dateTimeRegex = '~^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$~';

        $this->assertEquals(1, $lastInsertId);
        $this->assertEquals($ip, $row['ip']);
        $this->assertRegExp($dateTimeRegex, $row['dateTime']);
    }
}
