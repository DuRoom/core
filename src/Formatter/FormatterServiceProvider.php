<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Formatter;

use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Foundation\Paths;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Container\Container;

class FormatterServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->singleton('duroom.formatter', function (Container $container) {
            return new Formatter(
                new Repository($container->make('cache.filestore')),
                $container[Paths::class]->storage.'/formatter'
            );
        });

        $this->container->alias('duroom.formatter', Formatter::class);
    }
}
