<?php

use PHPUnit\Framework\TestCase;
use MetaRush\Firewall;
use MetaRush\DataMapper;

/**
 * Common setUp() and tearDown() for unit tests
 *
 * Note: Annotation in field methods are required for IDE's autocomplete
 */
class Common extends TestCase
{
    /**
     *
     * @var Firewall\Config
     */
    protected $cfg;

    /**
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     *
     * @var string
     */
    protected $dbFile;

    /**
     *
     * @var Firewall\Repo
     */
    protected $repo;

    /**
     *
     * @var DataMapper\DataMapper
     */
    protected $mapper;

    /**
     *
     * @var Firewall\Firewall
     */
    protected $firewall;

    public function setUp()
    {
        // ----------------------------------------------
        // setup Config object
        // ----------------------------------------------

        $this->cfg = (new Firewall\Config)
            ->setLightBanTable('lightBan')
            ->setExtendedBanTable('extendedBan')
            ->setWhitelistTable('whitelist')
            ->setFailCountTable('failCount')
            ->setBlockCountTable('blockCount');

        // ----------------------------------------------
        // setup test db
        // ----------------------------------------------

        $this->dbFile = __DIR__ . '/' . get_class($this) . '.db';
        // we use get_class($this) to use the child class' name as db name
        // because of IO issues with SQLite

        $dsn = 'sqlite:' . $this->dbFile;

        // create test db if doesn't exist yet
        if (!file_exists($this->dbFile)) {

            $this->pdo = new \PDO($dsn);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->pdo->query('
                CREATE TABLE `' . $this->cfg->getLightBanTable() . '` (
                `ip`        TEXT,
                `dateTime`  TEXT
            )');

            $this->pdo->query('
                CREATE TABLE `' . $this->cfg->getExtendedBanTable() . '` (
                `ip`        TEXT,
                `dateTime`  TEXT
            )');

            $this->pdo->query('
                CREATE TABLE `' . $this->cfg->getWhitelistTable() . '` (
                `ip`        TEXT,
                `dateTime`  TEXT
            )');

            $this->pdo->query('
                CREATE TABLE `' . $this->cfg->getFailCountTable() . '` (
                `ip`        TEXT,
                `dateTime`  TEXT
            )');

            $this->pdo->query('
                CREATE TABLE `' . $this->cfg->getBlockCountTable() . '` (
                `ip`        TEXT,
                `dateTime`  TEXT
            )');
        }

        // ----------------------------------------------
        // init main classes
        // ----------------------------------------------

        $builder = (new DataMapper\Builder)
            ->setDsn($dsn);

        $this->mapper = $builder->build();

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
