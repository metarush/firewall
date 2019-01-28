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
     * Block $ip if it's not whitelisted
     *
     * @param string $ip
     * @return void
     */
    public function addToBlacklist(string $ip): void
    {
        if (!$this->isWhitelisted($ip))
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

    /**
     * Block $ip if it reaches the value of Config->getFailCount()
     *
     * @param string $ip
     * @return void
     */
    public function preventBruteForce(string $ip): void
    {
        $this->repo->addIp($ip, $this->cfg->getFailCountTable(), true);

        $count = $this->repo->countIp($ip, $this->cfg->getFailCountTable());

        if ($count >= $this->cfg->getFailCount()) {
            $this->addToBlacklist($ip);
            $this->repo->addIp($ip, $this->cfg->getBlockCountTable());
            $this->repo->deleteIp($ip, $this->cfg->getFailCountTable());
        }
    }
}
