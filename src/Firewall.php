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
     * Light-ban $ip if it's not whitelisted
     *
     * @param string $ip
     * @return void
     */
    public function lightBan(string $ip): void
    {
        if ($this->whitelisted($ip))
            return;

        $this->repo->addIp($ip, $this->cfg->getLightBanTable());
        $this->repo->addIp($ip, $this->cfg->getBlockCountTable(), true);
        $this->repo->deleteIp($ip, $this->cfg->getFailCountTable());
    }

    /**
     * Extended-ban $ip if it's not whitelisted
     *
     * @param string $ip
     * @return void
     */
    public function extendedBan(string $ip): void
    {
        if ($this->whitelisted($ip))
            return;

        $this->repo->addIp($ip, $this->cfg->getExtendedBanTable());
        $this->repo->deleteIp($ip, $this->cfg->getFailCountTable());
        $this->repo->deleteIp($ip, $this->cfg->getLightBanTable());
    }

    /**
     * Returns true if $ip is banned (light/extended), false otherwise
     *
     * @param string $ip
     * @return bool
     */
    public function banned(string $ip): bool
    {
        if ($this->repo->ipLogged($ip, $this->cfg->getLightBanTable()))
            return true;

        if ($this->repo->ipLogged($ip, $this->cfg->getExtendedBanTable()))
            return true;

        return false;
    }

    /**
     * Whitelist $ip so it won't be banned no matter what
     *
     * @param string $ip
     * @return void
     */
    public function whitelist(string $ip): void
    {
        $this->repo->addIp($ip, $this->cfg->getWhitelistTable());
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
     * Light/extended ban $ip if it reaches the value of getMaxFailCount() and
     * getMaxBlockCount() respectively
     *
     * @param string $ip
     * @return void
     */
    public function preventBruteForce(string $ip): void
    {
        if ($this->whitelisted($ip))
            return;

        $this->repo->addIp($ip, $this->cfg->getFailCountTable(), true);

        $failCount = $this->repo->countIp($ip, $this->cfg->getFailCountTable());
        if ($failCount < $this->cfg->getMaxFailCount())
            return;

        $this->lightBan($ip);

        $blockCount = $this->repo->countIp($ip, $this->cfg->getBlockCountTable());
        if ($blockCount < $this->cfg->getMaxBlockCount())
            return;

        $this->extendedBan($ip);
    }

    /**
     * Release IPs that are banned (light/extended) for more than the set limit
     *
     * Run this on top of your script or via cron every x seconds
     *
     * @return void
     */
    public function flushExpired(): void
    {
        $this->repo->flushIps($this->cfg->getLightBanTable(), $this->cfg->getLightBanSeconds());
        $this->repo->flushIps($this->cfg->getExtendedBanTable(), $this->cfg->getExtendedBanSeconds());
    }

    /**
     * Release IPs that are light-banned
     *
     * @return void
     */
    public function flushLightBanned(): void
    {
        $this->repo->emptyTable($this->cfg->getLightBanTable());
    }

    /**
     * Release IPs that are extended-banned
     *
     * @return void
     */
    public function flushExtendedBanned(): void
    {
        $this->repo->emptyTable($this->cfg->getExtendedBanTable());
    }
}
