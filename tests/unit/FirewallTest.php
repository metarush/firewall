<?php

error_reporting(E_ALL);

use \PHPUnit\Framework\TestCase;
use MetaRush\Firewall;
use MetaRush\DataMapper;

class FirewallTest extends TestCase
{
    private $cfg;
    private $pdo;
    private $dbFile;
    private $repo;
    private $mapper;
    private $firewall;

    public function setUp()
    {
        // ----------------------------------------------
        // setup Config object
        // ----------------------------------------------

        $this->cfg = (new Firewall\Config)
            ->setBlacklistTable('blacklist');

        // ----------------------------------------------
        // setup test db
        // ----------------------------------------------

        $this->dbFile = __DIR__ . '/test.db';

        $dsn = 'sqlite:' . $this->dbFile;

        // create test db if doesn't exist yet
        if (!file_exists($this->dbFile)) {

            $this->pdo = new \PDO($dsn);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->pdo->query('
                CREATE TABLE `' . $this->cfg->getBlacklistTable() . '` (
                `id`        INTEGER PRIMARY KEY AUTOINCREMENT,
                `ip`        TEXT,
                `dateTime`  TEXT
            )');
        }

        // ----------------------------------------------
        // init Firewall
        // ----------------------------------------------

        $this->mapper = new DataMapper\DataMapper(
            new DataMapper\Adapters\AtlasQuery($dsn, null, null)
        );

        $this->repo = new Firewall\Repo($this->cfg, $this->mapper);

        $this->firewall = new Firewall\Firewall($this->cfg, $this->repo);
    }

    public function tearDown()
    {
        // close the DB connections so unlink will work
        unset($this->mapper);
        unset($this->pdo);
        unset($this->repo);
        unset($this->firewall);

        if (file_exists($this->dbFile))
            unlink($this->dbFile);
    }

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
