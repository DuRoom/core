<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api;

use DuRoom\Api\Controller\AbstractSerializeController;
use DuRoom\Api\Serializer\AbstractSerializer;
use DuRoom\Api\Serializer\BasicDiscussionSerializer;
use DuRoom\Api\Serializer\NotificationSerializer;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Foundation\ErrorHandling\JsonApiFormatter;
use DuRoom\Foundation\ErrorHandling\Registry;
use DuRoom\Foundation\ErrorHandling\Reporter;
use DuRoom\Http\Middleware as HttpMiddleware;
use DuRoom\Http\RouteCollection;
use DuRoom\Http\RouteHandlerFactory;
use DuRoom\Http\UrlGenerator;
use Illuminate\Contracts\Container\Container;
use Laminas\Stratigility\MiddlewarePipe;

class ApiServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->extend(UrlGenerator::class, function (UrlGenerator $url, Container $container) {
            return $url->addCollection('api', $container->make('duroom.api.routes'), 'api');
        });

        $this->container->singleton('duroom.api.routes', function () {
            $routes = new RouteCollection;
            $this->populateRoutes($routes);

            return $routes;
        });

        $this->container->singleton('duroom.api.throttlers', function () {
            return [
                'bypassThrottlingAttribute' => function ($request) {
                    if ($request->getAttribute('bypassThrottling')) {
                        return false;
                    }
                }
            ];
        });

        $this->container->bind(Middleware\ThrottleApi::class, function (Container $container) {
            return new Middleware\ThrottleApi($container->make('duroom.api.throttlers'));
        });

        $this->container->singleton('duroom.api.middleware', function () {
            return [
                HttpMiddleware\InjectActorReference::class,
                'duroom.api.error_handler',
                HttpMiddleware\ParseJsonBody::class,
                Middleware\FakeHttpMethods::class,
                HttpMiddleware\StartSession::class,
                HttpMiddleware\RememberFromCookie::class,
                HttpMiddleware\AuthenticateWithSession::class,
                HttpMiddleware\AuthenticateWithHeader::class,
                HttpMiddleware\SetLocale::class,
                'duroom.api.route_resolver',
                HttpMiddleware\CheckCsrfToken::class,
                Middleware\ThrottleApi::class
            ];
        });

        $this->container->bind('duroom.api.error_handler', function (Container $container) {
            return new HttpMiddleware\HandleErrors(
                $container->make(Registry::class),
                new JsonApiFormatter($container['duroom.config']->inDebugMode()),
                $container->tagged(Reporter::class)
            );
        });

        $this->container->bind('duroom.api.route_resolver', function (Container $container) {
            return new HttpMiddleware\ResolveRoute($container->make('duroom.api.routes'));
        });

        $this->container->singleton('duroom.api.handler', function (Container $container) {
            $pipe = new MiddlewarePipe;

            foreach ($this->container->make('duroom.api.middleware') as $middleware) {
                $pipe->pipe($container->make($middleware));
            }

            $pipe->pipe(new HttpMiddleware\ExecuteRoute());

            return $pipe;
        });

        $this->container->singleton('duroom.api.notification_serializers', function () {
            return [
                'discussionRenamed' => BasicDiscussionSerializer::class
            ];
        });

        $this->container->singleton('duroom.api_client.exclude_middleware', function () {
            return [
                HttpMiddleware\InjectActorReference::class,
                HttpMiddleware\ParseJsonBody::class,
                Middleware\FakeHttpMethods::class,
                HttpMiddleware\StartSession::class,
                HttpMiddleware\AuthenticateWithSession::class,
                HttpMiddleware\AuthenticateWithHeader::class,
                HttpMiddleware\CheckCsrfToken::class,
                HttpMiddleware\RememberFromCookie::class,
            ];
        });

        $this->container->singleton(Client::class, function ($container) {
            $pipe = new MiddlewarePipe;

            $exclude = $container->make('duroom.api_client.exclude_middleware');

            $middlewareStack = array_filter($container->make('duroom.api.middleware'), function ($middlewareClass) use ($exclude) {
                return ! in_array($middlewareClass, $exclude);
            });

            foreach ($middlewareStack as $middleware) {
                $pipe->pipe($container->make($middleware));
            }

            $pipe->pipe(new HttpMiddleware\ExecuteRoute());

            return new Client($pipe);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Container $container)
    {
        $this->setNotificationSerializers();

        AbstractSerializeController::setContainer($container);

        AbstractSerializer::setContainer($container);
    }

    /**
     * Register notification serializers.
     */
    protected function setNotificationSerializers()
    {
        $serializers = $this->container->make('duroom.api.notification_serializers');

        foreach ($serializers as $type => $serializer) {
            NotificationSerializer::setSubjectSerializer($type, $serializer);
        }
    }

    /**
     * Populate the API routes.
     *
     * @param RouteCollection $routes
     */
    protected function populateRoutes(RouteCollection $routes)
    {
        $factory = $this->container->make(RouteHandlerFactory::class);

        $callback = include __DIR__.'/routes.php';
        $callback($routes, $factory);
    }
}
