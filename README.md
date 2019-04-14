# metarush/firewall

A firewall library that web apps can use ban IP addresses temporarily or for an
extended period of time

---

### Sample Use Case

When a user fails an action in your app (e.g., login) for 5 times, this library
will lock them temporarily for 15 minutes. After that, they can try again for
a maximum of 5 temporary locks. After that, they will be locked for a extended
period of 24 hours.

### Summary

 - 5 fails within a 15-minute period = 15-minute temporary lock
 - 5 temporary locks = 24-hour lock

Note: These settings can be changed

## Install

Install via composer as `metarush/firewall`

## Setup

1. Create a database (with PDO support e.g., MySQL, SQLite).

2. Create tables with these names:

    - `tempBan`
    - `extendedBan`
    - `whitelist`
    - `failCount`
    - `blockCount`

    Note: You can use different table names but these are the default names

3. Each table must have the following fields:

 - `ip` (`STRING` with `45` length )
 - `dateTime` (`STRING` with `19` length)

Use the appropriate column type for your database flavor. E.g., `dateTime`
will store dates in `Y-m-d H:i:s` format so use `DATETIME` column type if your
database is MySQL.

**Sample create table query for MySQL**

    CREATE TABLE `tempBan` (
        `ip` VARCHAR(45),
        `dateTime` DATETIME
    ) ENGINE=MyISAM;


## Usage with default settings

### Init library

    <?php

    $builder = (new \MetaRush\Firewall\Builder)
        ->setDsn('mysql:host=localhost;dbname=yourfirewalldb')
        ->setDbUser('user')
        ->setDbPass('pass');

    $fw = $builder->build();

### Basic usage in your login code

    $fw->flushExpired(); // put this on top

    if ($fw->banned($_SERVER['REMOTE_ADDR'])) {
        exit('Forbidden'); // or redirect somewhere else
    }

    if ($_POST['password'] != 'foo') {
        $fw->preventBruteForce($_SERVER['REMOTE_ADDR']);
        // show your error page
    } else {
        // proceed to login
    }

## Custom settings

You can append the following methods upon class initialization:

### Table names

If you named your tables differently, let the system know via:

    ->setTempBanTable('tempBan')
    ->setExtendedBanTable('extendedBan')
    ->setWhitelistTable('whitelist')
    ->setFailCountTable('failCount')
    ->setBlockCountTable('blockCount')

### Max fail count before temporary ban

    ->setMaxFailCount(5)

### Temporary ban seconds

    ->setTempBanSeconds(900) // 15 minutes

### Max temporary ban before extended ban

    ->setMaxBlockCount(5)

### Extended ban seconds

    ->setExtendedBanSeconds(86400) // 1 day

### Period wherein failed attempts are counted as candidate for temporary ban

    ->setFailCountSeconds(900) // 15 minutes

### Period wherein temporary locks are counted as candidate for extended ban

    ->setBlockCountSeconds(86400) // 1 day

### Whitelist seconds

    ->setWhitelistSeconds(2592000) // 30 days

Note: The values displayed in the parameter are their default values. Each of these setter methods have their corresponding getter methods. E.g., `getMaxFailCount();`

## Apply custom settings

    $builder = (new \MetaRush\Firewall\Builder)
        ->setDsn('mysql:host=localhost;dbname=foo')
        ->setDbUser('user')
        ->setDbPass('pass')
        ->setTempBanTable('tempBan')
        ->setExtendedBanTable('extendedBan')
        ->setWhitelistTable('whitelist')
        ->setFailCountTable('failCount')
        ->setBlockCountTable('blockCount')
        ->setMaxFailCount(5)
        ->setTempBanSeconds(900)
        ->setMaxBlockCount(5)
        ->setExtendedBanSeconds(86400)
        ->setWhitelistSeconds(2592000);

    $fw = $builder->build();

## Available methods

You can use the following methods for your custom needs:

### tempBan

Ban `$ip` temporarily

`tempBan(string $ip): void`

### extendedBan

Ban `$ip` for an extended period

`extendedBan(string $ip): void`

### banned

Returns `true` if `$ip` is banned (temporarily or extended), `false` otherwise

`banned(string $ip): bool`

### whitelist

Whitelist `$ip` so it won't be banned no matter what

`whitelist(string $ip): void`

### whitelisted

Returns true if `$ip` is whitelisted, false otherwise

`whitelisted(string $ip): bool`

### preventBruteForce

Temporarily ban `$ip` if `getMaxFailCount()` is reached then ban `$ip` for an extended period if `getMaxBlockCount()` is reached

`preventBruteForce(string $ip): void`

### flushExpired

Release all IPs that are banned (temp/extended) and whitelisted for more than the set limit

`flushExpired(): void`

Note: Run this on top of your script or via cron regularly

### flushTempBanned

Release IPs that are temporarily banned regardless of expiration time

`flushTempBanned(): void`

### flushExtendedBanned

Release IPs that are banned for an extended period regardless of expiration time

`flushExtendedBanned(): void`

### flushWhitelisted

`flushWhitelisted(): void`

Release IPs that are whitelisted regardless of expiration time

### flushIp

Release IP in all "block" tables and optionally release in whitelist table

`flushIp(string $ip, bool $alsoWhitelistTable = false): void`