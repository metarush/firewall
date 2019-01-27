<?php

namespace MetaRush\Firewall;

class Config
{
    /**
     * Name of the blacklist database table
     *
     * @var string
     */
    private $blacklistTable;

    /**
     * Name of the whitelist database table
     *
     * @var string
     */
    private $whitelistTable;

    public function getBlacklistTable(): string
    {
        return $this->blacklistTable;
    }

    public function setBlacklistTable(string $blacklistTable)
    {
        $this->blacklistTable = $blacklistTable;

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
