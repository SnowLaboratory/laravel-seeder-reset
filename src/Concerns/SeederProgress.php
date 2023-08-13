<?php

namespace SnowBuilds\SeederReset\Concerns;

use Illuminate\Support\Collection;

trait SeederProgress {

    public function progressInitialize(int $count) {
        return $this->command->getOutput()->progressStart($count);
    }

    public function progressAdvance () {
        return $this->command->getOutput()->progressAdvance();
    }

    public function progressFinish () {
        return $this->command->getOutput()->progressFinish();
    }

    public function progressMap(Collection $list, callable $callback) {
        $this->progressInitialize($list->count());
        foreach($list as $key=>$value) {
            $callback($value, $key);
            $this->progressAdvance();
        }
        $this->progressFinish();
    }

}
