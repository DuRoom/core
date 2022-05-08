<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Install;

use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Http\RouteCollection;
use DuRoom\Http\RouteHandlerFactory;
use Illuminate\Contracts\Container\Container;

class InstallServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->singleton('duroom.install.routes', function () {
            return new RouteCollection;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Container $container, RouteHandlerFactory $route)
    {
        $this->loadViewsFrom(__DIR__.'/../../views/install', 'duroom.install');

        $this->populateRoutes($container->make('duroom.install.routes'), $route);
    }

    /**
     * @param RouteCollection     $routes
     * @param RouteHandlerFactory $route
     */
    protected function populateRoutes(RouteCollection $routes, RouteHandlerFactory $route)
    {
        $routes->get(
            '/{path:.*}',
            'index',
            $route->toController(Controller\IndexController::class)
        );

        $routes->post(
            '/{path:.*}',
            'install',
            $route->toController(Controller\InstallController::class)
        );
    }
}
