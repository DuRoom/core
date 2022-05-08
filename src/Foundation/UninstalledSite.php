<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Foundation;

use DuRoom\Install\Installer;
use DuRoom\Install\InstallServiceProvider;
use DuRoom\Locale\LocaleServiceProvider;
use DuRoom\Settings\SettingsRepositoryInterface;
use DuRoom\Settings\UninstalledSettingsRepository;
use DuRoom\User\SessionServiceProvider;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\FileViewFinder;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class UninstalledSite implements SiteInterface
{
    /**
     * @var Paths
     */
    protected $paths;

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(Paths $paths, string $baseUrl)
    {
        $this->paths = $paths;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Create and boot a DuRoom application instance.
     *
     * @return AppInterface
     */
    public function bootApp(): AppInterface
    {
        return new Installer(
            $this->bootLaravel()
        );
    }

    protected function bootLaravel(): Container
    {
        $container = new \Illuminate\Container\Container;
        $laravel = new Application($container, $this->paths);

        $container->instance('env', 'production');
        $container->instance('duroom.config', new Config(['url' => $this->baseUrl]));
        $container->alias('duroom.config', Config::class);
        $container->instance('duroom.debug', true);
        $container->instance('config', $config = $this->getIlluminateConfig());

        $this->registerLogger($container);

        $laravel->register(ErrorServiceProvider::class);
        $laravel->register(LocaleServiceProvider::class);
        $laravel->register(FilesystemServiceProvider::class);
        $laravel->register(SessionServiceProvider::class);
        $laravel->register(ValidationServiceProvider::class);

        $laravel->register(InstallServiceProvider::class);

        $container->singleton(
            SettingsRepositoryInterface::class,
            UninstalledSettingsRepository::class
        );

        $container->singleton('view', function ($container) {
            $engines = new EngineResolver();
            $engines->register('php', function () use ($container) {
                return $container->make(PhpEngine::class);
            });
            $finder = new FileViewFinder($container->make('files'), []);
            $dispatcher = $container->make(Dispatcher::class);

            return new \Illuminate\View\Factory(
                $engines,
                $finder,
                $dispatcher
            );
        });

        $laravel->boot();

        return $container;
    }

    /**
     * @return ConfigRepository
     */
    protected function getIlluminateConfig()
    {
        return new ConfigRepository([
            'session' => [
                'lifetime' => 120,
                'files' => $this->paths->storage.'/sessions',
                'cookie' => 'session'
            ],
            'view' => [
                'paths' => [],
            ],
        ]);
    }

    protected function registerLogger(Container $container)
    {
        $logPath = $this->paths->storage.'/logs/duroom-installer.log';
        $handler = new StreamHandler($logPath, Logger::DEBUG);
        $handler->setFormatter(new LineFormatter(null, null, true, true));

        $container->instance('log', new Logger('DuRoom Installer', [$handler]));
        $container->alias('log', LoggerInterface::class);
    }
}
