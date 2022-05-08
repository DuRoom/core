<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Http;

use DuRoom\Discussion\Discussion;
use DuRoom\Discussion\IdWithTransliteratedSlugDriver;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Settings\SettingsRepositoryInterface;
use DuRoom\User\IdSlugDriver;
use DuRoom\User\User;
use DuRoom\User\UsernameSlugDriver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;

class HttpServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->singleton('duroom.http.csrfExemptPaths', function () {
            return ['token'];
        });

        $this->container->bind(Middleware\CheckCsrfToken::class, function (Container $container) {
            return new Middleware\CheckCsrfToken($container->make('duroom.http.csrfExemptPaths'));
        });

        $this->container->singleton('duroom.http.slugDrivers', function () {
            return [
                Discussion::class => [
                    'default' => IdWithTransliteratedSlugDriver::class
                ],
                User::class => [
                    'default' => UsernameSlugDriver::class,
                    'id' => IdSlugDriver::class
                ],
            ];
        });

        $this->container->singleton('duroom.http.selectedSlugDrivers', function (Container $container) {
            $settings = $container->make(SettingsRepositoryInterface::class);

            $compiledDrivers = [];

            foreach ($container->make('duroom.http.slugDrivers') as $resourceClass => $resourceDrivers) {
                $driverKey = $settings->get("slug_driver_$resourceClass", 'default');

                $driverClass = Arr::get($resourceDrivers, $driverKey, $resourceDrivers['default']);

                $compiledDrivers[$resourceClass] = $container->make($driverClass);
            }

            return $compiledDrivers;
        });
        $this->container->bind(SlugManager::class, function (Container $container) {
            return new SlugManager($container->make('duroom.http.selectedSlugDrivers'));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->setAccessTokenTypes();
    }

    protected function setAccessTokenTypes()
    {
        $models = [
            DeveloperAccessToken::class,
            RememberAccessToken::class,
            SessionAccessToken::class
        ];

        foreach ($models as $model) {
            AccessToken::setModel($model::$type, $model);
        }
    }
}
