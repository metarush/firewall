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
     * Add $ip to $table
     *
     * @param string $ip
     * @param string $table
     * @param type $allowDuplicate
     * @return void
     */
    public function addIp(string $ip, string $table, $allowDuplicate = false): void
    {
        $data = [
            self::IP_COLUMN       => $ip,
            self::DATETIME_COLUMN => date('Y-m-d H:i:s')
        ];

        if (!$this->ipLogged($ip, $table) OR $allowDuplicate)
            $this->mapper->create($table, $data);
    }

    /**
     *  Returns true if $ip is logged in $table, false otherwise
     *
     * @param string $ip
     * @param string $table
     * @return bool
     */
    public function ipLogged(string $ip, string $table): bool
    {
        $where = [
            self::IP_COLUMN => trim($ip)
        ];

        $row = $this->mapper->findOne($table, $where);

        return is_array($row);
    }

    /**
     * Returns how many times $ip is logged inside $table
     *
     * @param string $ip
     * @param string $table
     * @return int
     */
    public function countIp(string $ip, string $table): int
    {
        $where = [
            self::IP_COLUMN => trim($ip)
        ];

        return count($this->mapper->findAll($table, $where));
    }

    /**
     * Delete $ip from $table
     *
     * @param string $ip
     * @param string $table
     * @return void
     */
    public function deleteIp(string $ip, string $table): void
    {
        $where = [
            self::IP_COLUMN => trim($ip)
        ];

        $this->mapper->delete($table, $where);
    }

    /**
     * Empty $table
     *
     * @param string $table
     * @return void
     */
    public function emptyTable(string $table): void
    {
        $this->mapper->delete($table);
    }
}
