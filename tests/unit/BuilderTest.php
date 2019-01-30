<?php

use MetaRush\Firewall;

require_once __DIR__ . '/Common.php';

class BuilderTest extends Common
{

    public function testBuilder()
    {
        $fwBuilder = (new Firewall\Builder)
            ->setDsn('sqlite:' . $this->dbFile);

        $firewall = $fwBuilder->build();

        $this->assertInstanceOf(Firewall\Firewall::class, $firewall);
    }
}
