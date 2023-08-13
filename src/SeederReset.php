<?php

namespace SnowBuilds\SeederReset;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SnowBuilds\SeederReset\Skeleton\SkeletonClass
 */
class SeederReset extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SeederResetManager::class;
    }
}
