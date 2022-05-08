<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Database;

use DuRoom\Foundation\AbstractServiceProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;

class DatabaseServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->singleton(Manager::class, function (Container $container) {
            $manager = new Manager($container);

            $config = $container['duroom']->config('database');
            $config['engine'] = 'InnoDB';
            $config['prefix_indexes'] = true;

            $manager->addConnection($config, 'duroom');

            return $manager;
        });

        $this->container->singleton(ConnectionResolverInterface::class, function (Container $container) {
            $manager = $container->make(Manager::class);
            $manager->setAsGlobal();
            $manager->bootEloquent();

            $dbManager = $manager->getDatabaseManager();
            $dbManager->setDefaultConnection('duroom');

            return $dbManager;
        });

        $this->container->alias(ConnectionResolverInterface::class, 'db');

        $this->container->singleton(ConnectionInterface::class, function (Container $container) {
            $resolver = $container->make(ConnectionResolverInterface::class);

            return $resolver->connection();
        });

        $this->container->alias(ConnectionInterface::class, 'db.connection');
        $this->container->alias(ConnectionInterface::class, 'duroom.db');

        $this->container->singleton(MigrationRepositoryInterface::class, function (Container $container) {
            return new DatabaseMigrationRepository($container['duroom.db'], 'migrations');
        });

        $this->container->singleton('duroom.database.model_private_checkers', function () {
            return [];
        });
    }

    public function boot(Container $container)
    {
        AbstractModel::setConnectionResolver($container->make(ConnectionResolverInterface::class));
        AbstractModel::setEventDispatcher($container->make('events'));

        foreach ($container->make('duroom.database.model_private_checkers') as $modelClass => $checkers) {
            $modelClass::saving(function ($instance) use ($checkers) {
                foreach ($checkers as $checker) {
                    if ($checker($instance) === true) {
                        $instance->is_private = true;

                        return;
                    }
                }

                $instance->is_private = false;
            });
        }
    }
}
