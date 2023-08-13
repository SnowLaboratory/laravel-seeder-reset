<?php

namespace SnowBuilds\SeederReset\Concerns;

use Illuminate\Support\Facades\DB;
use SnowBuilds\SeederReset\SeederReset;

trait SeederTruncate 
{
    public function __invoke(array $parameters = [])
    {
        SeederReset::set(static::class, 'parameters', $parameters);

        SeederReset::setSeeder($this);
        SeederReset::setCommand($this->command);
        
        SeederReset::execute();

        parent::__invoke($parameters);
    }

    public function truncate(array $tables) {
        SeederReset::truncate($tables);
    }
}
