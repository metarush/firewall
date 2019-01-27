<?php

namespace MetaRush\Firewall;

class Firewall
{
    private $repo;

    public function __construct(Repo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Blacklist an IP address
     *
     * @param string $ip
     * @return int
     */
    public function addToBlacklist(string $ip): int
    {
        return $this->repo->addToBlacklist($ip);
    }

    /**
     * Whitelist an IP address
     *
     * @param string $ip
     * @return int
     */
    public function addToWhitelist(string $ip): int
    {
        return $this->repo->addToWhitelist($ip);
    }
}
