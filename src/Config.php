<?php

namespace MetaRush\Firewall;

use MetaRush\DataAccess;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Config extends DataAccess\Config
{
    /**
     * Temp ban table
     *
     * @var string
     */
    private $tempBanTable = 'mrfwTempBan';

    /**
     * Extended ban table
     *
     * @var string
     */
    private $extendedBanTable = 'mrfwExtendedBan';

    /**
     * Whitelist table
     *
     * @var string
     */
    private $whitelistTable = 'mrfwWhitelist';

    /**
     * Fail count table
     *
     * @var string
     */
    private $failCountTable = 'mrfwFailCount';

    /**
     * Block count table
     *
     * @var string
     */
    private $blockCountTable = 'mrfwBlockCount';

    /**
     * Number of failed attempts before blocking an IP address (temp ban)
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
    private $tempBanSeconds = 900;

    /**
     * Number of seconds to ban IP address after reaching block count
     *
     * @var int
     */
    private $extendedBanSeconds = 86400;

    /**
     * Nuber of seconds an IP stays whitelisted
     *
     * @var int
     */
    private $whitelistSeconds = 2592000;

    /**
     * Number of seconds an IP stays in the failCount table
     *
     * @var int
     */
    private $failCountSeconds = 900;

    /**
     * Number of seconds an IP stays in the blockCount table
     *
     * @var int
     */
    private $blockCountSeconds = 86400;

    /**
     * PSR logger
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function getBlockCountSeconds(): int
    {
        return $this->blockCountSeconds;
    }

    public function setBlockCountSeconds(int $blockCountSeconds)
    {
        $this->blockCountSeconds = $blockCountSeconds;
        return $this;
    }

    public function getFailCountSeconds(): int
    {
        return $this->failCountSeconds;
    }

    public function setFailCountSeconds(int $failCountSeconds)
    {
        $this->failCountSeconds = $failCountSeconds;
        return $this;
    }

    public function getWhitelistSeconds(): int
    {
        return $this->whitelistSeconds;
    }

    public function setWhitelistSeconds(int $whitelistSeconds)
    {
        $this->whitelistSeconds = $whitelistSeconds;
        return $this;
    }

    public function getExtendedBanSeconds(): int
    {
        return $this->extendedBanSeconds;
    }

    public function setExtendedBanSeconds(int $extendedBanSeconds)
    {
        $this->extendedBanSeconds = $extendedBanSeconds;
        return $this;
    }

    public function getTempBanSeconds(): int
    {
        return $this->tempBanSeconds;
    }

    public function setTempBanSeconds(int $tempBanSeconds)
    {
        $this->tempBanSeconds = $tempBanSeconds;
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

    public function getTempBanTable(): string
    {
        return $this->tempBanTable;
    }

    public function setTempBanTable(string $tempBanTable)
    {
        $this->tempBanTable = $tempBanTable;
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

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getLogger(): LoggerInterface
    {
        $this->logger ??= new NullLogger();

        return $this->logger;
    }
}
