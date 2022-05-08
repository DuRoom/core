<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Admin;

use DuRoom\Extension\Event\Disabled;
use DuRoom\Extension\Event\Enabled;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Foundation\ErrorHandling\Registry;
use DuRoom\Foundation\ErrorHandling\Reporter;
use DuRoom\Foundation\ErrorHandling\ViewFormatter;
use DuRoom\Foundation\ErrorHandling\WhoopsFormatter;
use DuRoom\Foundation\Event\ClearingCache;
use DuRoom\Frontend\AddLocaleAssets;
use DuRoom\Frontend\AddTranslations;
use DuRoom\Frontend\Compiler\Source\SourceCollector;
use DuRoom\Frontend\RecompileFrontendAssets;
use DuRoom\Http\Middleware as HttpMiddleware;
use DuRoom\Http\RouteCollection;
use DuRoom\Http\RouteHandlerFactory;
use DuRoom\Http\UrlGenerator;
use DuRoom\Locale\LocaleManager;
use DuRoom\Settings\Event\Saved;
use Illuminate\Contracts\Container\Container;
use Laminas\Stratigility\MiddlewarePipe;

class AdminServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->extend(UrlGenerator::class, function (UrlGenerator $url, Container $container) {
            return $url->addCollection('admin', $container->make('duroom.admin.routes'), 'admin');
        });

        $this->container->singleton('duroom.admin.routes', function () {
            $routes = new RouteCollection;
            $this->populateRoutes($routes);

            return $routes;
        });

        $this->container->singleton('duroom.admin.middleware', function () {
            return [
                HttpMiddleware\InjectActorReference::class,
                'duroom.admin.error_handler',
                HttpMiddleware\ParseJsonBody::class,
                HttpMiddleware\StartSession::class,
                HttpMiddleware\RememberFromCookie::class,
                HttpMiddleware\AuthenticateWithSession::class,
                HttpMiddleware\SetLocale::class,
                'duroom.admin.route_resolver',
                HttpMiddleware\CheckCsrfToken::class,
                Middleware\RequireAdministrateAbility::class,
                HttpMiddleware\ReferrerPolicyHeader::class,
                HttpMiddleware\ContentTypeOptionsHeader::class,
                Middleware\DisableBrowserCache::class,
            ];
        });

        $this->container->bind('duroom.admin.error_handler', function (Container $container) {
            return new HttpMiddleware\HandleErrors(
                $container->make(Registry::class),
                $container['duroom.config']->inDebugMode() ? $container->make(WhoopsFormatter::class) : $container->make(ViewFormatter::class),
                $container->tagged(Reporter::class)
            );
        });

        $this->container->bind('duroom.admin.route_resolver', function (Container $container) {
            return new HttpMiddleware\ResolveRoute($container->make('duroom.admin.routes'));
        });

        $this->container->singleton('duroom.admin.handler', function (Container $container) {
            $pipe = new MiddlewarePipe;

            foreach ($container->make('duroom.admin.middleware') as $middleware) {
                $pipe->pipe($container->make($middleware));
            }

            $pipe->pipe(new HttpMiddleware\ExecuteRoute());

            return $pipe;
        });

        $this->container->bind('duroom.assets.admin', function (Container $container) {
            /** @var \DuRoom\Frontend\Assets $assets */
            $assets = $container->make('duroom.assets.factory')('admin');

            $assets->js(function (SourceCollector $sources) {
                $sources->addFile(__DIR__.'/../../js/dist/admin.js');
            });

            $assets->css(function (SourceCollector $sources) {
                $sources->addFile(__DIR__.'/../../less/admin.less');
            });

            $container->make(AddTranslations::class)->forFrontend('admin')->to($assets);
            $container->make(AddLocaleAssets::class)->to($assets);

            return $assets;
        });

        $this->container->bind('duroom.frontend.admin', function (Container $container) {
            /** @var \DuRoom\Frontend\Frontend $frontend */
            $frontend = $container->make('duroom.frontend.factory')('admin');

            $frontend->content($container->make(Content\AdminPayload::class));

            return $frontend;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../views', 'duroom.admin');

        $events = $this->container->make('events');

        $events->listen(
            [Enabled::class, Disabled::class, ClearingCache::class],
            function () {
                $recompile = new RecompileFrontendAssets(
                    $this->container->make('duroom.assets.admin'),
                    $this->container->make(LocaleManager::class)
                );
                $recompile->flush();
            }
        );

        $events->listen(
            Saved::class,
            function (Saved $event) {
                $recompile = new RecompileFrontendAssets(
                    $this->container->make('duroom.assets.admin'),
                    $this->container->make(LocaleManager::class)
                );
                $recompile->whenSettingsSaved($event);
            }
        );
    }

    /**
     * @param RouteCollection $routes
     */
    protected function populateRoutes(RouteCollection $routes)
    {
        $factory = $this->container->make(RouteHandlerFactory::class);

        $callback = include __DIR__.'/routes.php';
        $callback($routes, $factory);
    }
}
