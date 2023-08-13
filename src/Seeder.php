<?php

namespace SnowBuilds\SeederReset;

use Illuminate\Database\Seeder as DatabaseSeeder;
use SnowBuilds\SeederReset\Concerns\SeederTruncate;

class Seeder extends DatabaseSeeder
{
    use SeederTruncate;

    public function call($class, $silent = false, array $parameters = [])
    {
        $this->reset($class, $silent, $parameters, false);
        parent::call($class, $silent, $parameters);
    }
}
