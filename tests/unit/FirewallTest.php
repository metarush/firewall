<?php

require_once __DIR__ . '/Common.php';

class FirewallTest extends Common
{
    private $testIp = '1.2.3.4';
    private $where = ['ip' => '1.2.3.4'];
    private $data = ['ip' => '1.2.3.4'];

    /**
     * test tempBan
     */
    public function testTempBan()
    {
        $this->firewall->tempBan($this->testIp);

        // test if ip is temp banned
        $row = $this->dal->findOne($this->cfg->getTempBanTable(), $this->where);
        $this->assertIsArray($row);

        // test if ip is block counted
        $row = $this->dal->findOne($this->cfg->getBlockCountTable(), $this->where);
        $this->assertIsArray($row);

        // test if ip is no longer in failCountTable
        $row = $this->dal->findOne($this->cfg->getFailCountTable(), $this->where);
        $this->assertNull($row);
    }

    public function testTempBanWhitelisted()
    {
        // seed
        $this->dal->create($this->cfg->getWhitelistTable(), $this->data);

        $this->firewall->tempBan($this->testIp);

        // test if ip was not banned
        $row = $this->dal->findOne($this->cfg->getTempBanTable(), $this->where);
        $this->assertNull($row);
    }

    /**
     * test extendedBan
     */
    public function testExtendedBan()
    {
        $this->firewall->extendedBan($this->testIp);

        // test if ip is extended banned
        $row = $this->dal->findOne($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertIsArray($row);

        // test if ip is no longer in failCountTable
        $row = $this->dal->findOne($this->cfg->getFailCountTable(), $this->where);
        $this->assertNull($row);

        // test if ip is no longer in tempBanTable
        $row = $this->dal->findOne($this->cfg->getTempBanTable(), $this->where);
        $this->assertNull($row);
    }

    public function testExtendedBanWhitelisted()
    {
        // seed
        $this->dal->create($this->cfg->getWhitelistTable(), $this->data);

        $this->firewall->extendedBan($this->testIp);

        // test if ip was not banned
        $row = $this->dal->findOne($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertNull($row);
    }

    /**
     * test banned (temp ban)
     */
    public function testBannedTemp()
    {
        // seed
        $this->dal->create($this->cfg->getTempBanTable(), $this->data);

        $banned = $this->firewall->banned($this->testIp);

        $this->assertTrue($banned);
    }

    /**
     * test banned (extended ban)
     */
    public function testBannedExtended()
    {
        // seed
        $this->dal->create($this->cfg->getExtendedBanTable(), $this->data);

        $banned = $this->firewall->banned($this->testIp);

        $this->assertTrue($banned);
    }

    /**
     * test whitelist
     */
    public function testWhitelist()
    {
        $this->firewall->whitelist($this->testIp);

        $row = $this->dal->findOne($this->cfg->getWhitelistTable(), $this->where);
        $this->assertIsArray($row);
    }

    /**
     * test whitelisted
     */
    public function testWhitelisted()
    {
        // seed
        $this->dal->create($this->cfg->getWhitelistTable(), $this->data);

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
        $rows = $this->dal->findAll($this->cfg->getFailCountTable(), $this->where);
        $this->assertCount(3, $rows);
    }

    /**
     * Simulate 5 failed attempts so that the $ip will be transferred from
     * FailCountTable to TempBanTable.
     */
    public function testPreventBruteForceTempBan()
    {
        for ($i = 0; $i < 5; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        // test if FailCountTable is empty
        $rows = $this->dal->findAll($this->cfg->getFailCountTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if ip was transferred to tempBanTable
        $row = $this->dal->findOne($this->cfg->getTempBanTable(), $this->where);
        $this->assertIsArray($row);
    }

    /**
     * Simulate 25 failed attempts so that the $ip will be transferred from
     * TempBanTable to ExtendedBanTable
     */
    public function testPreventBruteForceExtendedBan()
    {
        for ($i = 0; $i < 25; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        // test if FailCountTable is empty
        $rows = $this->dal->findAll($this->cfg->getFailCountTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if ip is no longer in tempBanTable
        $row = $this->dal->findOne($this->cfg->getTempBanTable(), $this->where);
        $this->assertNull($row);

        // test if ip is now on extendedBanTable
        $row = $this->dal->findOne($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertIsArray($row);
    }

    public function testPreventBruteForceWhitelisted()
    {
        // seed
        $this->dal->create($this->cfg->getWhitelistTable(), $this->data);

        for ($i = 0; $i < 5; $i++)
            $this->firewall->preventBruteForce($this->testIp);

        // test if FailCountTable is empty
        $rows = $this->dal->findAll($this->cfg->getFailCountTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if ip is not in TempBanTable
        $row = $this->dal->findOne($this->cfg->getTempBanTable(), $this->where);
        $this->assertNull($row);
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
        $this->dal->create($this->cfg->getTempBanTable(), $data);
        $this->dal->create($this->cfg->getTempBanTable(), $data);
        $this->dal->create($this->cfg->getExtendedBanTable(), $data);
        $this->dal->create($this->cfg->getExtendedBanTable(), $data);
        $this->dal->create($this->cfg->getWhitelistTable(), $data);
        $this->dal->create($this->cfg->getWhitelistTable(), $data);
        $this->dal->create($this->cfg->getFailCountTable(), $data);
        $this->dal->create($this->cfg->getFailCountTable(), $data);
        $this->dal->create($this->cfg->getBlockCountTable(), $data);
        $this->dal->create($this->cfg->getBlockCountTable(), $data);

        $elapsedTime = 2;

        $this->cfg->setTempBanSeconds($elapsedTime);
        $this->cfg->setExtendedBanSeconds($elapsedTime);
        $this->cfg->setWhitelistSeconds($elapsedTime);
        $this->cfg->setFailCountSeconds($elapsedTime);
        $this->cfg->setBlockCountSeconds($elapsedTime);

        sleep(2); // value below $elapsedTime should fail this test

        $this->firewall->flushExpired();

        // test if IPs are flushed from TempBanTable
        $rows = $this->dal->findAll($this->cfg->getTempBanTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs are flushed from ExtendedBanTable
        $rows = $this->dal->findAll($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs are flushed from WhitelistTable
        $rows = $this->dal->findAll($this->cfg->getWhitelistTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs are flushed from FailCountTable
        $rows = $this->dal->findAll($this->cfg->getFailCountTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs are flushed from BlockCountTable
        $rows = $this->dal->findAll($this->cfg->getBlockCountTable(), $this->where);
        $this->assertCount(0, $rows);
    }

    /**
     * test flushTempBanned
     */
    public function testFlushTempBanned()
    {
        // seed
        $this->dal->create($this->cfg->getTempBanTable(), $this->data);
        $this->dal->create($this->cfg->getTempBanTable(), $this->data);

        $this->firewall->flushTempBanned();

        // test if IPs are flushed from TempBanTable
        $rows = $this->dal->findAll($this->cfg->getTempBanTable(), $this->where);
        $this->assertCount(0, $rows);
    }

    /**
     * test flushExtendedBanned
     */
    public function testFlushExtendedBanned()
    {
        // seed
        $this->dal->create($this->cfg->getExtendedBanTable(), $this->data);
        $this->dal->create($this->cfg->getExtendedBanTable(), $this->data);

        $this->firewall->flushExtendedBanned();

        // test if IPs are flushed from ExtendedBanTable
        $rows = $this->dal->findAll($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertCount(0, $rows);
    }

    /**
     * test flushWhitelisted
     */
    public function testFlushWhitelisted()
    {
        // seed
        $this->dal->create($this->cfg->getWhitelistTable(), $this->data);
        $this->dal->create($this->cfg->getWhitelistTable(), $this->data);

        $this->firewall->flushWhitelisted();

        $rows = $this->dal->findAll($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertCount(0, $rows);
    }

    /**
     * test flushIp
     */
    public function testFlushIp()
    {
        // seed
        $this->dal->create($this->cfg->getBlockCountTable(), $this->data);
        $this->dal->create($this->cfg->getExtendedBanTable(), $this->data);
        $this->dal->create($this->cfg->getFailCountTable(), $this->data);
        $this->dal->create($this->cfg->getTempBanTable(), $this->data);
        $this->dal->create($this->cfg->getWhitelistTable(), $this->data);

        $this->firewall->flushIp($this->testIp);

        // test if IPs are flushed from BlockCountTable
        $rows = $this->dal->findAll($this->cfg->getBlockCountTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs are flushed from ExtendedBanTable
        $rows = $this->dal->findAll($this->cfg->getExtendedBanTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs are flushed from FailCountTable
        $rows = $this->dal->findAll($this->cfg->getFailCountTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs are flushed from TempBanTable
        $rows = $this->dal->findAll($this->cfg->getTempBanTable(), $this->where);
        $this->assertCount(0, $rows);

        // test if IPs is NOT flushed from WhitelistTable
        $rows = $this->dal->findAll($this->cfg->getWhitelistTable(), $this->where);
        $this->assertCount(1, $rows);

        $this->firewall->flushIp($this->testIp, true);

        // test if IPs are flushed from WhitelistTable
        $rows = $this->dal->findAll($this->cfg->getWhitelistTable(), $this->where);
        $this->assertCount(0, $rows);
    }

    /**
     * test if a good IP is banned (for this test only: good means not banned)
     */
    public function testGoodIpBanned()
    {
        $goodIp = '9.9.9.9';

        $this->firewall->banned('example_good_ip');

        $row = $this->dal->findOne($this->cfg->getTempBanTable(), ['ip' => $goodIp]);

        $this->assertNull($row);
    }
}