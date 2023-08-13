<?php

namespace SnowBuilds\SeederReset;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class SeederResetManager
{
    private $registry;

    private Command $command;

    private Seeder $seeder;

    public function boot() {
        $this->registry = [];
    }

    public function set($class, $key, $value) {
        $data = data_get($this->registry, $class, []);
        $updated = data_set($data, $key, $value);
        data_set($this->registry, $class, $updated);
        return $this;
    }

    public function get($class, $key=null, $default=null) {
        $data = data_get($this->registry, $class, []);
        return data_get($data, $key, $default);
    }

    public function withoutConstraints(callable $callback): void
    {
        try {
            Schema::disableForeignKeyConstraints();
            call_user_func($callback);
            Schema::enableForeignKeyConstraints();
        } catch(Throwable $error) {
            throw $error;
        }
    }

    public function transactionWithoutConstraints(callable $callback): void
    {
        DB::beginTransaction();
        try {
            Schema::disableForeignKeyConstraints();
            call_user_func($callback);
            Schema::enableForeignKeyConstraints();
            DB::commit();
        } catch(Throwable $error) {
            DB::rollBack();
            $this->fail('Transaction failed. Rolled back data');
            throw $error;
        }
    }
    

    public function resolveTables(array $tables): Collection
    {
        return collect($tables)->map(function ($class) {
            if (class_exists($class)) {
                $instance = resolve($class);
            
                if ($instance instanceof Model) {
                    return $instance->getTable();
                } 

                return $this->resolveTables($this->getTruncated($instance));
            }

            return $class;
        })->flatten()->unique();
    }

    public function setCommand(Command $command): self
    {
        $this->command = $command;
        return $this;
    }

    public function setSeeder(Seeder $seeder): self
    {
        $this->seeder = $seeder;
        return $this;
    }

    public function getTruncated(Seeder $seeder)
    {
        $parameters = SeederReset::get($class = get_class($seeder), 'parameters', []);
        if (method_exists($class, 'getTruncate')) {
            return $seeder->getTruncate($parameters);
        } else if (property_exists($class, 'truncate')) {
            return $seeder->truncate;
        }

        return [];
    }

    public function processTables(array $tables) 
    {
        $tables = $this->resolveTables($tables);

        $parameters = $this->get(get_class($this->seeder), 'parameters');
        $truncate = data_get($parameters, 'truncate') ?? SeederReset::prompt($tables);

        if (! $truncate) {
            if (! data_get($parameters, 'ignoreSkip', count($tables) === 0)) {
                SeederReset::skip('Not truncating');
            }

            return false;
        }

        return tap($truncate, function () use ($tables) {
            $tables = $tables->toArray();
            $this->beforeTruncate($tables);

            method_exists($this->seeder, 'truncate')
                ? $this->seeder->truncate($tables)
                : $this->truncate($tables);
            
            $this->afterTruncate($tables);
            $count = count($tables);
            SeederReset::success(sprintf('%s %s truncated!', $count, Str::plural('table', $count)));
        });
    }

    public function execute()
    {
        return $this->processTables($this->getTruncated($this->seeder));
    }


    public function truncate(array $tables) {
        SeederReset::withoutConstraints(function () use ($tables) {
            foreach ($tables as $table) {
                SeederReset::info(sprintf('Truncating `%s`', $table));
                DB::table($table)->truncate();
            }
        });
    }

    public function beforeTruncate(array $tables) {
        $this->transactionWithoutConstraints(function () use ($tables) {
            if (method_exists($this->seeder, 'beforeTruncate')) {
                call_user_func([$this->seeder, 'beforeTruncate'], $tables);
            }
        });
    }

    public function afterTruncate(array $tables) {
        $this->transactionWithoutConstraints(function () use ($tables) {
            if (method_exists($this->seeder, 'beforeTruncate')) {
                call_user_func([$this->seeder, 'beforeTruncate'], $tables);
            }
        });
    }

    public function prompt(Collection $tables): bool
    {
        $count = $tables->count();
        $nameOutput = $tables->map(fn($name) => " - " . $name)->join(PHP_EOL);
        return $count > 0 && $this->command->confirm(implode(PHP_EOL, [
            sprintf("You are about to truncate %s %s! This action cannot be undone:", $count, Str::plural('table', $count) ),
            $nameOutput,
            "",
            "Continue truncating?"
        ]));
    }

    public function info(string $message): void
    {
        $this->command->getOutput()->block(
            messages: $message,
            type: 'INFO',
            style: 'fg=white',
        );
    }

    public function warn(string $message): void
    {
        $this->command->getOutput()->block(
            messages: $message,
            type: 'WARNING',
            style: 'fg=yellow',
        );
    }

    public function fail(string $message): void
    {
        $this->command->getOutput()->block(
            messages: $message,
            type: 'ERROR',
            style: 'fg=white;bg=red',
            padding: true
        );
    }

    public function skip(string $message): void
    {
        $this->command->getOutput()->block(
            messages: $message,
            type: 'SKIPPING',
            style: 'fg=yellow',
        );
    }

    public function success(string $message): void
    {
        $this->command->getOutput()->block(
            messages: $message,
            type: 'DONE',
            style: 'fg=green',
        );
    }
}
