<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Http\Middleware;

use DuRoom\Foundation\Config;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;

class DuRoomPromotionHeader implements Middleware
{
    protected $enabled = true;

    public function __construct(Config $config)
    {
        $this->enabled = Arr::get($config, 'headers.poweredByHeader') ?? true;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($this->enabled) {
            $response = $response->withAddedHeader('X-Powered-By', 'DuRoom');
        }

        return $response;
    }
}
