<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Console;

use DuRoom\Console\Cache\Factory;
use DuRoom\Database\Console\MigrateCommand;
use DuRoom\Database\Console\ResetCommand;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Foundation\Console\AssetsPublishCommand;
use DuRoom\Foundation\Console\CacheClearCommand;
use DuRoom\Foundation\Console\InfoCommand;
use Illuminate\Console\Scheduling\CacheEventMutex;
use Illuminate\Console\Scheduling\CacheSchedulingMutex;
use Illuminate\Console\Scheduling\EventMutex;
use Illuminate\Console\Scheduling\Schedule as LaravelSchedule;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Console\Scheduling\SchedulingMutex;
use Illuminate\Contracts\Container\Container;

class ConsoleServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Used by Laravel to proxy artisan commands to its binary.
        // DuRoom uses a similar binary, but it's called duroom.
        if (! defined('ARTISAN_BINARY')) {
            define('ARTISAN_BINARY', 'duroom');
        }

        // DuRoom doesn't fully use Laravel's cache system, but rather
        // creates and binds a single cache store.
        // See \DuRoom\Foundation\InstalledSite::registerCache
        // Since certain config options (e.g. withoutOverlapping, onOneServer)
        // need the cache, we must override the cache factory we give to the scheduling
        // mutexes so it returns our single custom cache.
        $this->container->bind(EventMutex::class, function ($container) {
            return new CacheEventMutex($container->make(Factory::class));
        });
        $this->container->bind(SchedulingMutex::class, function ($container) {
            return new CacheSchedulingMutex($container->make(Factory::class));
        });

        $this->container->singleton(LaravelSchedule::class, function (Container $container) {
            return $container->make(Schedule::class);
        });

        $this->container->singleton('duroom.console.commands', function () {
            return [
                AssetsPublishCommand::class,
                CacheClearCommand::class,
                InfoCommand::class,
                MigrateCommand::class,
                ResetCommand::class,
                ScheduleListCommand::class,
                ScheduleRunCommand::class
                // Used internally to create DB dumps before major releases.
                // \DuRoom\Database\Console\GenerateDumpCommand::class
            ];
        });

        $this->container->singleton('duroom.console.scheduled', function () {
            return [];
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Container $container)
    {
        $schedule = $container->make(LaravelSchedule::class);

        foreach ($container->make('duroom.console.scheduled') as $scheduled) {
            $event = $schedule->command($scheduled['command'], $scheduled['args']);
            $scheduled['callback']($event);
        }
    }
}
