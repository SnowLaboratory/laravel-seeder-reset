<?php

namespace SnowBuilds\SeederReset\Concerns;

use Illuminate\Support\Arr;
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

    public function reset($class, $silent = false, array $parameters = [], $call=true) {
        $classes = Arr::wrap($class);

        SeederReset::processTables($classes);

        $parameters = array_merge([
            'truncate' => false,
            'ignoreSkip' => true,
            'silent' => $silent,
            $parameters
        ]);

        if($call) {
            $this->call($class, $silent, $parameters);
        }

        SeederReset::boot();
    }
}
