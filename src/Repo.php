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
     * Add IP address to table
     *
     * @param string $ip
     * @param string $table
     * @return void
     */
    public function addIp(string $ip, string $table): void
    {
        $data = [
            self::IP_COLUMN       => $ip,
            self::DATETIME_COLUMN => date('Y-m-d H:i:s')
        ];

        $this->mapper->create($table, $data);
    }

    /**
     *  Returns true if $ip is logged in $table, false otherwise
     *
     * @param string $ip
     * @param string $table
     * @return bool
     */
    public function isIpLogged(string $ip, string $table): bool
    {
        $where = [
            self::IP_COLUMN => trim($ip)
        ];

        $row = $this->mapper->findOne($table, $where);

        return is_array($row);
    }
}
