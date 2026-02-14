<?php

use PHPUnit\Framework\TestCase;
use MetaRush\Firewall;
use MetaRush\DataAccess;

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
     * @var DataAccess\DataAccess
     */
    protected $dal;

    /**
     *
     * @var Firewall\Firewall
     */
    protected $firewall;

    public function setUp(): void
    {
        // ----------------------------------------------
        // setup Config object
        // ----------------------------------------------

        $this->cfg = (new Firewall\Config)
            ->setTempBanTable('tempBan')
            ->setExtendedBanTable('extendedBan')
            ->setWhitelistTable('whitelist')
            ->setFailCountTable('failCount')
            ->setBlockCountTable('blockCount')
            ->setMaxBlockCount(5)
            ->setMaxFailCount(5)
            ->setLogger(new Psr\Log\NullLogger());

        // ----------------------------------------------
        // setup test db
        // ----------------------------------------------

        $this->dbFile = __DIR__ . '/' . get_class($this) . '-' . uniqid() . '.db';
        // we use get_class($this) to use the child class' name as db name
        // because of IO issues with SQLite

        $dsn = 'sqlite:' . $this->dbFile;

        // create test db if doesn't exist yet
        if (!file_exists($this->dbFile)) {

            $this->pdo = new \PDO($dsn);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->pdo->query('
                CREATE TABLE `' . $this->cfg->getTempBanTable() . '` (
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

        $builder = (new DataAccess\Builder)
            ->setDsn($dsn);

        $this->dal = $builder->build();

        $this->repo = new Firewall\Repo($this->cfg, $this->dal);

        $this->firewall = new Firewall\Firewall($this->cfg, $this->repo);
    }

    public function tearDown(): void
    {
        // close the DB connections so unlink will work
        unset($this->dal);
        unset($this->pdo);
        unset($this->repo);
        unset($this->firewall);

        if (file_exists($this->dbFile))
            unlink($this->dbFile);
    }
}
