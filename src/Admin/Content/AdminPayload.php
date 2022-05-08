<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Admin\Content;

use DuRoom\Extension\ExtensionManager;
use DuRoom\Frontend\Document;
use DuRoom\Group\Permission;
use DuRoom\Settings\Event\Deserializing;
use DuRoom\Settings\SettingsRepositoryInterface;
use DuRoom\User\User;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminPayload
{
    /**
     * @var Container;
     */
    protected $container;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var ExtensionManager
     */
    protected $extensions;

    /**
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * @param Container $container
     * @param SettingsRepositoryInterface $settings
     * @param ExtensionManager $extensions
     * @param ConnectionInterface $db
     * @param Dispatcher $events
     */
    public function __construct(
        Container $container,
        SettingsRepositoryInterface $settings,
        ExtensionManager $extensions,
        ConnectionInterface $db,
        Dispatcher $events
    ) {
        $this->container = $container;
        $this->settings = $settings;
        $this->extensions = $extensions;
        $this->db = $db;
        $this->events = $events;
    }

    public function __invoke(Document $document, Request $request)
    {
        $settings = $this->settings->all();

        $this->events->dispatch(
            new Deserializing($settings)
        );

        $document->payload['settings'] = $settings;
        $document->payload['permissions'] = Permission::map();
        $document->payload['extensions'] = $this->extensions->getExtensions()->toArray();

        $document->payload['displayNameDrivers'] = array_keys($this->container->make('duroom.user.display_name.supported_drivers'));
        $document->payload['slugDrivers'] = array_map(function ($resourceDrivers) {
            return array_keys($resourceDrivers);
        }, $this->container->make('duroom.http.slugDrivers'));

        $document->payload['phpVersion'] = PHP_VERSION;
        $document->payload['mysqlVersion'] = $this->db->selectOne('select version() as version')->version;

        /**
         * Used in the admin user list. Implemented as this as it matches the API in duroom/statistics.
         * If duroom/statistics ext is enabled, it will override this data with its own stats.
         *
         * This allows the front-end code to be simpler and use one single source of truth to pull the
         * total user count from.
         */
        $document->payload['modelStatistics'] = [
            'users' => [
                'total' => User::count()
            ]
        ];
    }
}
