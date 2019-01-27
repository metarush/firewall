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

    public function getBlacklistTable(): string
    {
        return $this->blacklistTable;
    }

    public function setBlacklistTable(string $blacklistTable)
    {
        $this->blacklistTable = $blacklistTable;

        return $this;
    }
}
