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
     * Blacklist $ip if it's not whitelisted
     *
     * @param string $ip
     * @return void
     */
    public function blacklist(string $ip): void
    {
        if (!$this->whitelisted($ip))
            $this->repo->addIp($ip, $this->cfg->getBlacklistTable());
    }

    /**
     * Whitelist $ip so it won't be blocked no matter what
     *
     * @param string $ip
     * @return void
     */
    public function whitelist(string $ip): void
    {
        $this->repo->addIp($ip, $this->cfg->getWhitelistTable());
    }

    /**
     * Returns true if $ip is blacklisted, false otherwise
     *
     * @param string $ip
     * @return bool
     */
    public function blackListed(string $ip): bool
    {
        return $this->repo->ipLogged($ip, $this->cfg->getBlacklistTable());
    }

    /**
     * Returns true if $ip is whitelisted, false otherwise
     *
     * @param string $ip
     * @return bool
     */
    public function whitelisted(string $ip): bool
    {
        return $this->repo->ipLogged($ip, $this->cfg->getWhitelistTable());
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
            $this->blacklist($ip);
            $this->repo->addIp($ip, $this->cfg->getBlockCountTable());
            $this->repo->deleteIp($ip, $this->cfg->getFailCountTable());
        }
    }
}
