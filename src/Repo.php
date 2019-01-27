<?php

namespace MetaRush\Firewall;

use MetaRush\DataMapper;

class Repo
{
    const IP_COLUMN = 'ip';
    const DATETIME_COLUMN = 'dateTime';
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
            self::IP_COLUMN       => trim($ip),
            self::DATETIME_COLUMN => date('Y-m-d H:i:s')
        ];

        return $this->mapper->create($this->cfg->getBlacklistTable(), $data);
    }

    /**
     * Whitelist an IP address
     *
     * @param string $ip
     * @return int
     */
    public function addToWhitelist(string $ip): int
    {
        $data = [
            self::IP_COLUMN       => trim($ip),
            self::DATETIME_COLUMN => date('Y-m-d H:i:s')
        ];

        return $this->mapper->create($this->cfg->getWhitelistTable(), $data);
    }

    /**
     * Returns true if $ip is blacklisted, false otherwise
     *
     * @param string $ip
     * @return bool
     */
    public function isBlacklisted(string $ip): bool
    {
        $where = [
            self::IP_COLUMN => trim($ip)
        ];

        $row = $this->mapper->findOne($this->cfg->getBlacklistTable(), $where);

        return is_array($row);
    }
}
