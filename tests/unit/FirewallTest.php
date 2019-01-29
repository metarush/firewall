<?php

require_once __DIR__ . '/Common.php';

class FirewallTest extends Common
{
    private $testIp = '1.2.3.4';
    private $where = ['ip' => '1.2.3.4'];
    private $data = ['ip' => '1.2.3.4'];

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
        // seed
        $this->mapper->create($this->cfg->getWhitelistTable(), $this->data);

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
        // seed
        $this->mapper->create($this->cfg->getLightBanTable(), $this->data);

        $banned = $this->firewall->banned($this->testIp);

        $this->assertTrue($banned);
    }

    /**
     * test banned (extended ban)
     */
    public function testBannedExtended()
    {
        // seed
        $this->mapper->create($this->cfg->getExtendedBanTable(), $this->data);

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
        // seed
        $this->mapper->create($this->cfg->getWhitelistTable(), $this->data);

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

    /**
     * test flushExpired
     */
    public function testFlushExpired()
    {
        // seed
        $data = [
            'ip'       => '1.2.3.4',
            'dateTime' => date('Y-m-d H:i:s')
        ];
        $this->mapper->create($this->cfg->getLightBanTable(), $data);
        $this->mapper->create($this->cfg->getLightBanTable(), $data);
        $this->mapper->create($this->cfg->getExtendedBanTable(), $data);
        $this->mapper->create($this->cfg->getExtendedBanTable(), $data);

        $elapsedTime = 2;

        $this->cfg->setLightBanSeconds($elapsedTime);
        $this->cfg->setExtendedBanSeconds($elapsedTime);

        sleep(2); // value below $elapsedTime should fail this test

        $this->firewall->flushExpired();

        // test if IPs are flushed from LightBanTable
        $rows = $this->mapper->findAll($this->cfg->getLightBanTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs are flushed from ExtendedBanTable
        $rows = $this->mapper->findAll($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertCount(0, $rows);
    }

    public function testFlushLightBanned()
    {
        // seed
        $this->mapper->create($this->cfg->getLightBanTable(), $this->data);
        $this->mapper->create($this->cfg->getLightBanTable(), $this->data);

        $this->firewall->flushLightBanned();

        // test if IPs are flushed from LightBanTable
        $rows = $this->mapper->findAll($this->cfg->getLightBanTable(), $this->where);
        $this->assertCount(0, $rows);
    }

    public function testFlushExtendedBanned()
    {
        // seed
        $this->mapper->create($this->cfg->getExtendedBanTable(), $this->data);
        $this->mapper->create($this->cfg->getExtendedBanTable(), $this->data);

        $this->firewall->flushExtendedBanned();

        // test if IPs are flushed from ExtendedBanTable
        $rows = $this->mapper->findAll($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertCount(0, $rows);
    }
}
