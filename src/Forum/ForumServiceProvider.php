<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Forum;

use DuRoom\Extension\Event\Disabled;
use DuRoom\Extension\Event\Enabled;
use DuRoom\Formatter\Formatter;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Foundation\ErrorHandling\Registry;
use DuRoom\Foundation\ErrorHandling\Reporter;
use DuRoom\Foundation\ErrorHandling\ViewFormatter;
use DuRoom\Foundation\ErrorHandling\WhoopsFormatter;
use DuRoom\Foundation\Event\ClearingCache;
use DuRoom\Frontend\AddLocaleAssets;
use DuRoom\Frontend\AddTranslations;
use DuRoom\Frontend\Assets;
use DuRoom\Frontend\Compiler\Source\SourceCollector;
use DuRoom\Frontend\RecompileFrontendAssets;
use DuRoom\Http\Middleware as HttpMiddleware;
use DuRoom\Http\RouteCollection;
use DuRoom\Http\RouteHandlerFactory;
use DuRoom\Http\UrlGenerator;
use DuRoom\Locale\LocaleManager;
use DuRoom\Settings\Event\Saved;
use DuRoom\Settings\Event\Saving;
use DuRoom\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Laminas\Stratigility\MiddlewarePipe;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForumServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->extend(UrlGenerator::class, function (UrlGenerator $url, Container $container) {
            return $url->addCollection('forum', $container->make('duroom.forum.routes'));
        });

        $this->container->singleton('duroom.forum.routes', function (Container $container) {
            $routes = new RouteCollection;
            $this->populateRoutes($routes, $container);

            return $routes;
        });

        $this->container->afterResolving('duroom.forum.routes', function (RouteCollection $routes, Container $container) {
            $this->setDefaultRoute($routes, $container);
        });

        $this->container->singleton('duroom.forum.middleware', function () {
            return [
                HttpMiddleware\InjectActorReference::class,
                'duroom.forum.error_handler',
                HttpMiddleware\ParseJsonBody::class,
                HttpMiddleware\CollectGarbage::class,
                HttpMiddleware\StartSession::class,
                HttpMiddleware\RememberFromCookie::class,
                HttpMiddleware\AuthenticateWithSession::class,
                HttpMiddleware\SetLocale::class,
                'duroom.forum.route_resolver',
                HttpMiddleware\CheckCsrfToken::class,
                HttpMiddleware\ShareErrorsFromSession::class,
                HttpMiddleware\DuRoomPromotionHeader::class,
                HttpMiddleware\ReferrerPolicyHeader::class,
                HttpMiddleware\ContentTypeOptionsHeader::class
            ];
        });

        $this->container->bind('duroom.forum.error_handler', function (Container $container) {
            return new HttpMiddleware\HandleErrors(
                $container->make(Registry::class),
                $container['duroom.config']->inDebugMode() ? $container->make(WhoopsFormatter::class) : $container->make(ViewFormatter::class),
                $container->tagged(Reporter::class)
            );
        });

        $this->container->bind('duroom.forum.route_resolver', function (Container $container) {
            return new HttpMiddleware\ResolveRoute($container->make('duroom.forum.routes'));
        });

        $this->container->singleton('duroom.forum.handler', function (Container $container) {
            $pipe = new MiddlewarePipe;

            foreach ($container->make('duroom.forum.middleware') as $middleware) {
                $pipe->pipe($container->make($middleware));
            }

            $pipe->pipe(new HttpMiddleware\ExecuteRoute());

            return $pipe;
        });

        $this->container->bind('duroom.assets.forum', function (Container $container) {
            /** @var Assets $assets */
            $assets = $container->make('duroom.assets.factory')('forum');

            $assets->js(function (SourceCollector $sources) use ($container) {
                $sources->addFile(__DIR__.'/../../js/dist/forum.js');
                $sources->addString(function () use ($container) {
                    return $container->make(Formatter::class)->getJs();
                });
            });

            $assets->css(function (SourceCollector $sources) use ($container) {
                $sources->addFile(__DIR__.'/../../less/forum.less');
                $sources->addString(function () use ($container) {
                    return $container->make(SettingsRepositoryInterface::class)->get('custom_less', '');
                });
            });

            $container->make(AddTranslations::class)->forFrontend('forum')->to($assets);
            $container->make(AddLocaleAssets::class)->to($assets);

            return $assets;
        });

        $this->container->bind('duroom.frontend.forum', function (Container $container) {
            return $container->make('duroom.frontend.factory')('forum');
        });
    }

    public function boot(Container $container, Dispatcher $events, Factory $view)
    {
        $this->loadViewsFrom(__DIR__.'/../../views', 'duroom.forum');

        $view->share([
            'translator' => $container->make(TranslatorInterface::class),
            'settings' => $container->make(SettingsRepositoryInterface::class)
        ]);

        $events->listen(
            [Enabled::class, Disabled::class, ClearingCache::class],
            function () use ($container) {
                $recompile = new RecompileFrontendAssets(
                    $container->make('duroom.assets.forum'),
                    $container->make(LocaleManager::class)
                );
                $recompile->flush();
            }
        );

        $events->listen(
            Saved::class,
            function (Saved $event) use ($container) {
                $recompile = new RecompileFrontendAssets(
                    $container->make('duroom.assets.forum'),
                    $container->make(LocaleManager::class)
                );
                $recompile->whenSettingsSaved($event);

                $validator = new ValidateCustomLess(
                    $container->make('duroom.assets.forum'),
                    $container->make('duroom.locales'),
                    $container,
                    $container->make('duroom.less.config')
                );
                $validator->whenSettingsSaved($event);
            }
        );

        $events->listen(
            Saving::class,
            function (Saving $event) use ($container) {
                $validator = new ValidateCustomLess(
                    $container->make('duroom.assets.forum'),
                    $container->make('duroom.locales'),
                    $container,
                    $container->make('duroom.less.config')
                );
                $validator->whenSettingsSaving($event);
            }
        );
    }

    /**
     * Populate the forum client routes.
     *
     * @param RouteCollection $routes
     * @param Container       $container
     */
    protected function populateRoutes(RouteCollection $routes, Container $container)
    {
        $factory = $container->make(RouteHandlerFactory::class);

        $callback = include __DIR__.'/routes.php';
        $callback($routes, $factory);
    }

    /**
     * Determine the default route.
     *
     * @param RouteCollection $routes
     * @param Container       $container
     */
    protected function setDefaultRoute(RouteCollection $routes, Container $container)
    {
        $factory = $container->make(RouteHandlerFactory::class);
        $defaultRoute = $container->make('duroom.settings')->get('default_route');

        if (isset($routes->getRouteData()[0]['GET'][$defaultRoute]['handler'])) {
            $toDefaultController = $routes->getRouteData()[0]['GET'][$defaultRoute]['handler'];
        } else {
            $toDefaultController = $factory->toForum(Content\Index::class);
        }

        $routes->get(
            '/',
            'default',
            $toDefaultController
        );
    }
}
