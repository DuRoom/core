<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Settings;

use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Settings\Event\Saving;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;

class SettingsServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->singleton('duroom.settings.default', function () {
            return new Collection([
                'theme_primary_color' => '#4D698E',
                'theme_secondary_color' => '#4D698E',
            ]);
        });

        $this->container->singleton(SettingsRepositoryInterface::class, function (Container $container) {
            return new DefaultSettingsRepository(
                new MemoryCacheSettingsRepository(
                    new DatabaseSettingsRepository(
                        $container->make(ConnectionInterface::class)
                    )
                ),
                $container->make('duroom.settings.default')
            );
        });

        $this->container->alias(SettingsRepositoryInterface::class, 'duroom.settings');
    }

    public function boot(Dispatcher $events, SettingsValidator $settingsValidator)
    {
        $events->listen(
            Saving::class,
            function (Saving $event) use ($settingsValidator) {
                $settingsValidator->assertValid($event->settings);
            }
        );
    }
}
