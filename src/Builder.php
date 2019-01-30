<?php

namespace MetaRush\Firewall;

use MetaRush\DataMapper;

class Builder extends Config
{

    public function build(): Firewall
    {
        $dMBuilder = (new DataMapper\Builder)
            ->setAdapter($this->getAdapter())
            ->setDsn($this->getDsn())
            ->setDbUser($this->getDbUser())
            ->setDbPass($this->getDbPass());

        $mapper = $dMBuilder->build();

        $cfg = $this;

        $repo = new Repo($cfg, $mapper);

        return new Firewall($cfg, $repo);
    }
}
