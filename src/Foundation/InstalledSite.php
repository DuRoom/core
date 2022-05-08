<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Foundation;

use DuRoom\Admin\AdminServiceProvider;
use DuRoom\Api\ApiServiceProvider;
use DuRoom\Bus\BusServiceProvider;
use DuRoom\Console\ConsoleServiceProvider;
use DuRoom\Database\DatabaseServiceProvider;
use DuRoom\Discussion\DiscussionServiceProvider;
use DuRoom\Extension\ExtensionServiceProvider;
use DuRoom\Filesystem\FilesystemServiceProvider;
use DuRoom\Filter\FilterServiceProvider;
use DuRoom\Formatter\FormatterServiceProvider;
use DuRoom\Forum\ForumServiceProvider;
use DuRoom\Frontend\FrontendServiceProvider;
use DuRoom\Group\GroupServiceProvider;
use DuRoom\Http\HttpServiceProvider;
use DuRoom\Locale\LocaleServiceProvider;
use DuRoom\Mail\MailServiceProvider;
use DuRoom\Notification\NotificationServiceProvider;
use DuRoom\Post\PostServiceProvider;
use DuRoom\Queue\QueueServiceProvider;
use DuRoom\Search\SearchServiceProvider;
use DuRoom\Settings\SettingsServiceProvider;
use DuRoom\Update\UpdateServiceProvider;
use DuRoom\User\SessionServiceProvider;
use DuRoom\User\UserServiceProvider;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class InstalledSite implements SiteInterface
{
    /**
     * @var Paths
     */
    protected $paths;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \DuRoom\Extend\ExtenderInterface[]
     */
    protected $extenders = [];

    public function __construct(Paths $paths, Config $config)
    {
        $this->paths = $paths;
        $this->config = $config;
    }

    /**
     * Create and boot a DuRoom application instance.
     *
     * @return InstalledApp
     */
    public function bootApp(): AppInterface
    {
        return new InstalledApp(
            $this->bootLaravel(),
            $this->config
        );
    }

    /**
     * @param \DuRoom\Extend\ExtenderInterface[] $extenders
     * @return InstalledSite
     */
    public function extendWith(array $extenders): self
    {
        $this->extenders = $extenders;

        return $this;
    }

    protected function bootLaravel(): Container
    {
        $container = new \Illuminate\Container\Container;
        $laravel = new Application($container, $this->paths);

        $container->instance('env', 'production');
        $container->instance('duroom.config', $this->config);
        $container->alias('duroom.config', Config::class);
        $container->instance('duroom.debug', $this->config->inDebugMode());
        $container->instance('config', $config = $this->getIlluminateConfig($laravel));
        $container->instance('duroom.maintenance.handler', new MaintenanceModeHandler);

        $this->registerLogger($container);
        $this->registerCache($container);

        $laravel->register(AdminServiceProvider::class);
        $laravel->register(ApiServiceProvider::class);
        $laravel->register(BusServiceProvider::class);
        $laravel->register(ConsoleServiceProvider::class);
        $laravel->register(DatabaseServiceProvider::class);
        $laravel->register(DiscussionServiceProvider::class);
        $laravel->register(ExtensionServiceProvider::class);
        $laravel->register(ErrorServiceProvider::class);
        $laravel->register(FilesystemServiceProvider::class);
        $laravel->register(FilterServiceProvider::class);
        $laravel->register(FormatterServiceProvider::class);
        $laravel->register(ForumServiceProvider::class);
        $laravel->register(FrontendServiceProvider::class);
        $laravel->register(GroupServiceProvider::class);
        $laravel->register(HashServiceProvider::class);
        $laravel->register(HttpServiceProvider::class);
        $laravel->register(LocaleServiceProvider::class);
        $laravel->register(MailServiceProvider::class);
        $laravel->register(NotificationServiceProvider::class);
        $laravel->register(PostServiceProvider::class);
        $laravel->register(QueueServiceProvider::class);
        $laravel->register(SearchServiceProvider::class);
        $laravel->register(SessionServiceProvider::class);
        $laravel->register(SettingsServiceProvider::class);
        $laravel->register(UpdateServiceProvider::class);
        $laravel->register(UserServiceProvider::class);
        $laravel->register(ValidationServiceProvider::class);
        $laravel->register(ViewServiceProvider::class);

        $laravel->booting(function () use ($container) {
            // Run all local-site extenders before booting service providers
            // (but after those from "real" extensions, which have been set up
            // in a service provider above).
            foreach ($this->extenders as $extension) {
                $extension->extend($container);
            }
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
            'app' => [
                'timezone' => 'UTC'
            ],
            'view' => [
                'paths' => [],
                'compiled' => $this->paths->storage.'/views',
            ],
            'session' => [
                'lifetime' => 120,
                'files' => $this->paths->storage.'/sessions',
                'cookie' => 'session'
            ]
        ]);
    }

    protected function registerLogger(Container $container)
    {
        $logPath = $this->paths->storage.'/logs/duroom.log';
        $logLevel = $this->config->inDebugMode() ? Logger::DEBUG : Logger::INFO;
        $handler = new RotatingFileHandler($logPath, 0, $logLevel);
        $handler->setFormatter(new LineFormatter(null, null, true, true));

        $container->instance('log', new Logger('duroom', [$handler]));
        $container->alias('log', LoggerInterface::class);
    }

    protected function registerCache(Container $container)
    {
        $container->singleton('cache.store', function ($container) {
            return new CacheRepository($container->make('cache.filestore'));
        });
        $container->alias('cache.store', Repository::class);

        $container->singleton('cache.filestore', function () {
            return new FileStore(new Filesystem, $this->paths->storage.'/cache');
        });
        $container->alias('cache.filestore', Store::class);
    }
}
