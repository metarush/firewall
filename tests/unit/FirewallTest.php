<?php

error_reporting(E_ALL);

use \PHPUnit\Framework\TestCase;

class FirewallTest extends TestCase
{
    private $pdo;
    private $dbFile;
    private $blacklistTable;

    public function setUp()
    {
        // ----------------------------------------------
        // setup test db
        // ----------------------------------------------

        $this->dbFile = __DIR__ . '/test.db';
        $this->blacklistTable = 'blacklist';

        $dsn = 'sqlite:' . $this->dbFile;

        // create test db if doesn't exist yet
        if (!file_exists($this->dbFile)) {

            $this->pdo = new \PDO($dsn);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->pdo->query('
                CREATE TABLE `' . $this->blacklistTable . '` (
                `id`        INTEGER PRIMARY KEY AUTOINCREMENT,
                `ip`        TEXT,
                `dateTime`  TEXT
            )');
        }
    }

    public function tearDown()
    {
        // close the DB connections so unlink will work
        //unset($this->mapper);
        unset($this->pdo);

        if (file_exists($this->dbFile))
            unlink($this->dbFile);
    }

    public function testAddToBlacklist()
    {

    }
}
