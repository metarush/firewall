<?php

error_reporting(E_ALL);

require_once __DIR__ . '/Common.php';

class RepoTest extends Common
{
    private $testIp = '1.2.3.4';
    private $where = ['ip' => '1.2.3.4'];
    private $data = ['ip' => '1.2.3.4'];
    private $testTable = 'lightBan';

    /**
     * test addIp
     */
    public function testAddIp()
    {
        $this->repo->addIp($this->testIp, $this->testTable);

        $row = $this->mapper->findOne($this->testTable, []);
        $this->assertEquals($this->testIp, $row['ip']);

        $dateTimeRegex = '~^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$~';
        $this->assertRegExp($dateTimeRegex, $row['dateTime']);
    }

    /**
     * test addIp with $allowDuplicate param
     */
    public function testAddIpAllowDuplicate()
    {
        for ($i = 0; $i < 3; $i++)
            $this->repo->addIp($this->testIp, $this->testTable, true);

        $rows = $this->mapper->findAll($this->testTable, $this->where);
        $this->assertCount(3, $rows);
    }

    /**
     * test ipLogged
     *
     */
    public function testIpLogged()
    {
        // seed data
        $this->mapper->create($this->testTable, $this->data);

        $logged = $this->repo->ipLogged($this->testIp, $this->testTable);

        $this->assertTrue($logged);
    }

    /**
     * test countIp
     */
    public function testCountIp()
    {
        // seed data
        for ($i = 0; $i < 5; $i++)
            $this->mapper->create($this->testTable, $this->data);

        $count = $this->repo->countIp($this->testIp, $this->testTable);

        $this->assertEquals(5, $count);
    }

    /**
     * test deleteIp
     */
    public function testDeleteIp()
    {
        // seed data
        for ($i = 0; $i < 5; $i++)
            $this->mapper->create($this->testTable, $this->data);

        $this->repo->deleteIp($this->testIp, $this->testTable);

        $rows = $this->mapper->findAll($this->testTable, $this->where);

        $this->assertCount(0, $rows);
    }

    /**
     * test emptyTable
     */
    public function testEmptyTable()
    {
        // seed data
        for ($i = 0; $i < 5; $i++)
            $this->mapper->create($this->testTable, $this->data);

        $this->repo->emptyTable($this->testTable);

        $rows = $this->mapper->findAll($this->testTable);

        $this->assertCount(0, $rows);
    }

    /**
     * test flushIps
     */
    public function testFlushIps()
    {
        $data = [
            'ip'       => '1.2.3.4',
            'dateTime' => date('Y-m-d H:i:s')
        ];

        // seed data
        $this->mapper->create($this->testTable, $data);

        $elapsedTime = 2;

        // simulate elapsed time (value lower than $elapsedTime should fail this test)
        sleep($elapsedTime);

        $this->repo->flushIps($this->testTable, $elapsedTime);

        $row = $this->mapper->findOne($this->testTable, $this->where);
        $this->assertNull($row);
    }
}
