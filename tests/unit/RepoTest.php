<?php

error_reporting(E_ALL);

use \PHPUnit\Framework\TestCase;
use MetaRush\Firewall;
use MetaRush\DataMapper;

class RepoTest extends TestCase
{
    private $cfg;
    private $pdo;
    private $dbFile;
    private $repo;
    private $mapper;

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
        // init Repo
        // ----------------------------------------------

        $this->mapper = new DataMapper\DataMapper(
            new DataMapper\Adapters\AtlasQuery($dsn, null, null)
        );

        $this->repo = new Firewall\Repo($this->cfg, $this->mapper);
    }

    public function tearDown()
    {
        // close the DB connections so unlink will work
        unset($this->mapper);
        unset($this->pdo);
        unset($this->repo);

        if (file_exists($this->dbFile))
            unlink($this->dbFile);
    }

    public function testAddToBlacklist()
    {
        $lastInsertId = $this->repo->addToBlacklist('123');

        $this->assertEquals(1, $lastInsertId);
    }
}
