<?php

error_reporting(E_ALL);

require_once __DIR__ . '/Common.php';

class FirewallTest extends Common
{
    private $testIp = '1.2.3.4';

    public function testPreventBruteForce()
    {
        // ----------------------------------------------
        // simulate 5 failed attempts so that the $ip will be transferred from
        // FailCountTable to BlacklistTable
        // ----------------------------------------------
        for ($i = 0; $i < 5; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        $where = [
            'ip' => $this->testIp
        ];

        $rows = $this->mapper->findAll($this->cfg->getFailCountTable(), $where);

        // check if FailCountTable is empty
        $this->assertCount(0, $rows);

        $logged = $this->repo->ipLogged($this->testIp, $this->cfg->getBlacklistTable());

        // check if $ip was transferred to BlacklistTable
        $this->assertTrue($logged);
    }
}
