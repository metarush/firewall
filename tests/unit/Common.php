<?php

error_reporting(E_ALL);

use \PHPUnit\Framework\TestCase;
use MetaRush\Firewall;
use MetaRush\DataMapper;

class Common extends TestCase
{
    protected $cfg;
    protected $pdo;
    protected $dbFile;
    protected $repo;
    protected $mapper;
    protected $firewall;

    public function setUp()
    {
        // ----------------------------------------------
        // setup Config object
        // ----------------------------------------------

        $this->cfg = (new Firewall\Config)
            ->setBlacklistTable('blacklist')
            ->setWhitelistTable('whitelist');

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

            $this->pdo->query('
                CREATE TABLE `' . $this->cfg->getWhitelistTable() . '` (
                `id`        INTEGER PRIMARY KEY AUTOINCREMENT,
                `ip`        TEXT,
                `dateTime`  TEXT
            )');
        }

        // ----------------------------------------------
        // init main classes
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
}
