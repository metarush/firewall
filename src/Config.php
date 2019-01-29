<?php

namespace MetaRush\Firewall;

class Config
{
    /**
     * Name of the light ban database table
     *
     * @var string
     */
    private $lightBanTable;

    /**
     * Name of the whitelist database table
     *
     * @var string
     */
    private $whitelistTable;

    /**
     * Name of the database table that will store fail count
     *
     * @var string
     */
    private $failCountTable;

    /**
     * Name of the database table that will store block count
     *
     * @var string
     */
    private $blockCountTable;

    /**
     * Number of failed attempts before blocking an IP address (light ban)
     *
     * @var int
     */
    private $failCount = 5;

    /**
     * Number of blocks an IP address must get before getting an extended ban
     *
     * @var int
     */
    private $blockCount = 5;

    /**
     * Number of seconds to ban IP address after reaching fail count
     *
     * @var int
     */
    private $lightBanSeconds = 900;

    /**
     * Number of seconds to ban IP address after reaching block count
     *
     * @var int
     */
    private $extendedBanSeconds = 86400;

    public function getExtendedBanSeconds(): int
    {
        return $this->extendedBanSeconds;
    }

    public function setExtendedBanSeconds(int $extendedBanSeconds)
    {
        $this->extendedBanSeconds = $extendedBanSeconds;

        return $this;
    }

    public function getLightBanSeconds(): int
    {
        return $this->lightBanSeconds;
    }

    public function setLightBanSeconds(int $lightBanSeconds)
    {
        $this->lightBanSeconds = $lightBanSeconds;

        return $this;
    }

    public function getBlockCount(): int
    {
        return $this->blockCount;
    }

    public function setBlockCount(int $blockCount)
    {
        $this->blockCount = $blockCount;

        return $this;
    }

    public function getFailCount(): int
    {
        return $this->failCount;
    }

    public function setFailCount(int $failCount)
    {
        $this->failCount = $failCount;

        return $this;
    }

    public function getBlockCountTable(): string
    {
        return $this->blockCountTable;
    }

    public function setBlockCountTable(string $blockCountTable)
    {
        $this->blockCountTable = $blockCountTable;

        return $this;
    }

    public function getFailCountTable(): string
    {
        return $this->failCountTable;
    }

    public function setFailCountTable(string $failCountTable)
    {
        $this->failCountTable = $failCountTable;

        return $this;
    }

    public function getLightBanTable(): string
    {
        return $this->lightBanTable;
    }

    public function setLightBanTable(string $lightBanTable)
    {
        $this->lightBanTable = $lightBanTable;

        return $this;
    }

    public function getWhitelistTable(): string
    {
        return $this->whitelistTable;
    }

    public function setWhitelistTable(string $whitelistTable)
    {
        $this->whitelistTable = $whitelistTable;

        return $this;
    }
}
