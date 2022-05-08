<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extension;

use DuRoom\Extension\Event\Disabling;
use DuRoom\Foundation\AbstractServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;

class ExtensionServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->singleton(ExtensionManager::class);
        $this->container->alias(ExtensionManager::class, 'duroom.extensions');

        // Boot extensions when the app is booting. This must be done as a boot
        // listener on the app rather than in the service provider's boot method
        // below, so that extensions have a chance to register things on the
        // container before the core boots up (and starts resolving services).
        $this->container['duroom']->booting(function () {
            $this->container->make('duroom.extensions')->extend($this->container);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Dispatcher $events)
    {
        $events->listen(
            Disabling::class,
            DefaultLanguagePackGuard::class
        );
    }
}
