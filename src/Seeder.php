<?php

namespace SnowBuilds\SeederReset;

use Illuminate\Database\Seeder as DatabaseSeeder;
use Illuminate\Support\Arr;
use SnowBuilds\SeederReset\Concerns\SeederTruncate;

class Seeder extends DatabaseSeeder
{
    public function call($class, $silent = false, array $parameters = [])
    {
        $classes = Arr::wrap($class);
   
        SeederReset::set(static::class, 'parameters', $parameters);

        SeederReset::setSeeder($this);
        SeederReset::setCommand($this->command);
        
        SeederReset::processTables($classes);

        $parameters = array_merge([
            'truncate' => false,
            'ignoreSkip' => true,
            $parameters
        ]);

        parent::call($class, $silent, $parameters);
    }
}