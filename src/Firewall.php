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
     * Blacklist an IP address
     *
     * @param string $ip
     * @return void
     */
    public function addToBlacklist(string $ip): void
    {
        $this->repo->addIp($ip, $this->cfg->getBlacklistTable());
    }

    /**
     * Whitelist an IP address
     *
     * @param string $ip
     * @return void
     */
    public function addToWhitelist(string $ip): void
    {
        $this->repo->addIp($ip, $this->cfg->getWhitelistTable());
    }

    /**
     * Returns true if $ip is blacklisted, false otherwise
     *
     * @param string $ip
     * @return bool
     */
    public function isBlacklisted(string $ip): bool
    {
        return $this->repo->isIpLogged($ip, $this->cfg->getBlacklistTable());
    }

    /**
     * Returns true if $ip is whitelisted, false otherwise
     *
     * @param string $ip
     * @return bool
     */
    public function isWhitelisted(string $ip): bool
    {
        return $this->repo->isIpLogged($ip, $this->cfg->getWhitelistTable());
    }

    public function countFailThenBlock(string $ip)
    {

    }
}
