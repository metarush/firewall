<?php

namespace MetaRush\Firewall;

use MetaRush\DataMapper;

class Repo
{
    private $cfg;
    private $mapper;

    public function __construct(Config $cfg, DataMapper\DataMapper $mapper)
    {
        $this->cfg = $cfg;
        $this->mapper = $mapper;
    }

    /**
     * Blacklist an IP address
     *
     * @param string $ip
     * @return int
     */
    public function addToBlacklist(string $ip): int
    {
        $data = [
            'ip'       => trim($ip),
            'dateTime' => date('Y-m-d H:i:s')
        ];

        return $this->mapper->create($this->cfg->getBlacklistTable(), $data);
    }

    public function addToWhitelist(string $ip): int
    {
        $data = [
            'ip'       => trim($ip),
            'dateTime' => date('Y-m-d H:i:s')
        ];

        return $this->mapper->create($this->cfg->getWhitelistTable(), $data);
    }
}
