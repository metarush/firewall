<?php

require_once __DIR__ . '/Common.php';

class FirewallTest extends Common
{
    private $testIp = '1.2.3.4';
    private $where = ['ip' => '1.2.3.4'];

    /**
     * test lightBan
     */
    public function testLightBan()
    {
        $this->firewall->lightBan($this->testIp);

        // test if ip is light banned
        $row = $this->mapper->findOne($this->cfg->getLightBanTable(), $this->where);
        $this->assertIsArray($row);

        // test if ip is block counted
        $row = $this->mapper->findOne($this->cfg->getBlockCountTable(), $this->where);
        $this->assertIsArray($row);

        // test if ip is no longer in failCountTable
        $row = $this->mapper->findOne($this->cfg->getFailCountTable(), $this->where);
        $this->assertNull($row);
    }

    public function testLightBanWhitelisted()
    {
        // seed data
        $this->mapper->create($this->cfg->getWhitelistTable(), ['ip' => $this->testIp]);

        $this->firewall->lightBan($this->testIp);

        // test if ip was not banned
        $row = $this->mapper->findOne($this->cfg->getLightBanTable(), $this->where);
        $this->assertNull($row);
    }

    /**
     * test extendedBan
     */
    public function testExtendedBan()
    {
        $this->firewall->extendedBan($this->testIp);

        // test if ip is extended banned
        $row = $this->mapper->findOne($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertIsArray($row);

        // test if ip is no longer in failCountTable
        $row = $this->mapper->findOne($this->cfg->getFailCountTable(), $this->where);
        $this->assertNull($row);

        // test if ip is no longer in lightBanTable
        $row = $this->mapper->findOne($this->cfg->getLightBanTable(), $this->where);
        $this->assertNull($row);
    }

    /**
     * test banned (light ban)
     */
    public function testBannedLight()
    {
        // seed data
        $this->mapper->create($this->cfg->getLightBanTable(), ['ip' => $this->testIp]);

        $banned = $this->firewall->banned($this->testIp);

        $this->assertTrue($banned);
    }

    /**
     * test banned (extended ban)
     */
    public function testBannedExtended()
    {
        // seed data
        $this->mapper->create($this->cfg->getExtendedBanTable(), ['ip' => $this->testIp]);

        $banned = $this->firewall->banned($this->testIp);

        $this->assertTrue($banned);
    }

    /**
     * test whitelist
     */
    public function testWhitelist()
    {
        $this->firewall->whitelist($this->testIp);

        $row = $this->mapper->findOne($this->cfg->getWhitelistTable(), $this->where);
        $this->assertIsArray($row);
    }

    /**
     * test whitelisted
     */
    public function testWhitelisted()
    {
        // seed data
        $this->mapper->create($this->cfg->getWhitelistTable(), ['ip' => $this->testIp]);

        $whitelisted = $this->firewall->whitelisted($this->testIp);

        $this->assertTrue($whitelisted);
    }

    /**
     * Simulate 3 failed attempts so that the $ip will added to FailCountTable
     */
    public function testPreventBruteForceFailCount()
    {
        for ($i = 0; $i < 3; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        // test if there are 3 rows in FailCountTable
        $rows = $this->mapper->findAll($this->cfg->getFailCountTable(), $this->where);
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

        // test if FailCountTable is empty
        $rows = $this->mapper->findAll($this->cfg->getFailCountTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if ip was transferred to lightBanTable
        $row = $this->mapper->findOne($this->cfg->getLightBanTable(), $this->where);
        $this->assertIsArray($row);
    }

    /**
     * Simulate 25 failed attempts so that the $ip will be transferred from
     * LightBanTable to ExtendedBanTable
     */
    public function testPreventBruteForceExtendedBan()
    {
        for ($i = 0; $i < 25; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        // test if FailCountTable is empty
        $rows = $this->mapper->findAll($this->cfg->getFailCountTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if ip is no longer in lightBanTable
        $row = $this->mapper->findOne($this->cfg->getLightBanTable(), $this->where);
        $this->assertNull($row);

        // test if ip is now on extendedBanTable
        $row = $this->mapper->findOne($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertIsArray($row);
    }
}
