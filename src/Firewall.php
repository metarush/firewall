<?php

namespace MetaRush\Firewall;

class Firewall
{
    private $cfg;
    private $repo;

    public function __construct(Config $cfg, Repo $repo)
    {
        $this->cfg = $cfg;
        $this->repo = $repo;
    }

    /**
     * Add an IP address to blacklist
     *
     * @param string $ip
     * @return int
     */
    public function addToBlacklist(string $ip): int
    {
        return $this->repo->addToBlacklist($ip);
    }
}