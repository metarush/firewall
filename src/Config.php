<?php

namespace MetaRush\Firewall;

use MetaRush\DataMapper;

class Config extends DataMapper\Config
{
    /**
     * Light ban table
     *
     * @var string
     */
    private $lightBanTable = 'lightBan';

    /**
     * Extended ban table
     *
     * @var string
     */
    private $extendedBanTable = 'extendedBan';

    /**
     * Whitelist table
     *
     * @var string
     */
    private $whitelistTable = 'whitelist';

    /**
     * Fail count table
     *
     * @var string
     */
    private $failCountTable = 'failCount';

    /**
     * Block count table
     *
     * @var string
     */
    private $blockCountTable = 'blockCount';

    /**
     * Number of failed attempts before blocking an IP address (light ban)
     *
     * @var int
     */
    private $maxFailCount = 5;

    /**
     * Number of blocks an IP address must get before getting an extended ban
     *
     * @var int
     */
    private $maxBlockCount = 5;

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

    public function getMaxBlockCount(): int
    {
        return $this->maxBlockCount;
    }

    public function setMaxBlockCount(int $maxBlockCount)
    {
        $this->maxBlockCount = $maxBlockCount;

        return $this;
    }

    public function getMaxFailCount(): int
    {
        return $this->maxFailCount;
    }

    public function setMaxFailCount(int $maxFailCount)
    {
        $this->maxFailCount = $maxFailCount;

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

    public function getExtendedBanTable(): string
    {
        return $this->extendedBanTable;
    }

    public function setExtendedBanTable(string $extendedBanTable)
    {
        $this->extendedBanTable = $extendedBanTable;

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
