<?php

require_once __DIR__ . '/Common.php';

class FirewallTest extends Common
{
    private $testIp = '1.2.3.4';
    private $where = ['ip' => '1.2.3.4'];

    /**
     * Simulate 3 failed attempts so that the $ip will added to FailCountTable
     */
    public function testPreventBruteForceFailCount()
    {
        for ($i = 0; $i < 3; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        $rows = $this->mapper->findAll($this->cfg->getFailCountTable(), $this->where);

        // check if there are 3 rows in FailCountTable
        $this->assertCount(3, $rows);
    }

    /**
     * Simulate 5 failed attempts so that the $ip will be transferred from
     * FailCountTable to LightBanTable.
     */
    public function testPreventBruteForceLightBan()
    {
        for ($i = 0; $i < 5; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        $rows = $this->mapper->findAll($this->cfg->getFailCountTable(), $this->where);

        // check if FailCountTable is empty
        $this->assertCount(0, $rows);

        $logged = $this->repo->ipLogged($this->testIp, $this->cfg->getLightBanTable());

        // check if $ip was transferred to BlacklistTable
        $this->assertTrue($logged);
    }

    /**
     * Simulate 25 failed attempts so that the $ip will be transferred from
     * LightBanTable to ExtendedBanTable
     */
    public function testPreventBruteForceExtendedBan()
    {
        for ($i = 0; $i < 25; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        $rows = $this->mapper->findAll($this->cfg->getFailCountTable(), $this->where);

        // check if FailCountTable is empty
        $this->assertCount(0, $rows);

        $lightBanned = $this->repo->ipLogged($this->testIp, $this->cfg->getLightBanTable());

        // check if $ip is no longer in lightBanTable
        $this->assertFalse($lightBanned);

        $extendedBanned = $this->repo->ipLogged($this->testIp, $this->cfg->getExtendedBanTable());

        // check if $ip is now on extendedBanTable
        $this->assertTrue($extendedBanned);
    }
}
