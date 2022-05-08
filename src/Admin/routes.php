<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use DuRoom\Admin\Content\Index;
use DuRoom\Admin\Controller\UpdateExtensionController;
use DuRoom\Http\RouteCollection;
use DuRoom\Http\RouteHandlerFactory;

return function (RouteCollection $map, RouteHandlerFactory $route) {
    $map->get(
        '/',
        'index',
        $route->toAdmin(Index::class)
    );

    $map->post(
        '/extensions/{name}',
        'extensions.update',
        $route->toController(UpdateExtensionController::class)
    );
};
