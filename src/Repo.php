<?php

namespace MetaRush\Firewall;

use MetaRush\DataAccess\DataAccess;

class Repo
{
    const IP_COLUMN = 'ip';
    const DATETIME_COLUMN = 'dateTime';

    private Config $cfg;
    private DataAccess $dal;

    public function __construct(Config $cfg, DataAccess $dal)
    {
        $this->cfg = $cfg;
        $this->dal = $dal;
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

        if (!$this->ipLogged($ip, $table) OR $allowDuplicate) {

            $this->dal->create($table, $data);

            $this->log('IP logged in ' . $table . ': ' . $ip);
        }
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

        $row = $this->dal->findOne($table, $where);

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

        return count($this->dal->findAll($table, $where));
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

        $this->dal->delete($table, $where);

        $this->log('IP deleted in ' . $table . ': ' . $ip);
    }

    /**
     * Empty $table
     *
     * @param string $table
     * @return void
     */
    public function emptyTable(string $table): void
    {
        $this->dal->delete($table);

        $this->log('Table ' . $table . ' emptied');
    }

    /**
     * Flush IPs in $table that are there for more than $elapsed seconds
     *
     * @param string $table
     * @param int $elapsed
     * @return void
     */
    public function flushIps(string $table, int $elapsed): void
    {
        $strtotime = strtotime('-' . $elapsed . ' seconds');
        $past = date('Y-m-d H:i:s', $strtotime);

        $where = [
            self::DATETIME_COLUMN . "  <=  '" . $past . "'"
        ];

        $this->dal->delete($table, $where);

        $this->log('Flushed IPs in ' . $table . ' that are more than ' . $elapsed . 's old');
    }

    /**
     *
     * @param string $msg
     * @return void
     */
    protected function log(string $msg): void
    {
        $logger = $this->cfg->getLogger();

        $logger->debug('[MRF] ' . $msg);
    }
}