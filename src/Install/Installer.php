<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Install;

use DuRoom\Foundation\AppInterface;
use DuRoom\Foundation\ErrorHandling\Registry;
use DuRoom\Foundation\ErrorHandling\Reporter;
use DuRoom\Foundation\ErrorHandling\WhoopsFormatter;
use DuRoom\Http\Middleware as HttpMiddleware;
use DuRoom\Install\Console\InstallCommand;
use Illuminate\Contracts\Container\Container;
use Laminas\Stratigility\MiddlewarePipe;

class Installer implements AppInterface
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Psr\Http\Server\RequestHandlerInterface
     */
    public function getRequestHandler()
    {
        $pipe = new MiddlewarePipe;
        $pipe->pipe(new HttpMiddleware\HandleErrors(
            $this->container->make(Registry::class),
            $this->container->make(WhoopsFormatter::class),
            $this->container->tagged(Reporter::class)
        ));
        $pipe->pipe($this->container->make(HttpMiddleware\StartSession::class));
        $pipe->pipe(
            new HttpMiddleware\ResolveRoute($this->container->make('duroom.install.routes'))
        );
        $pipe->pipe(new HttpMiddleware\ExecuteRoute());

        return $pipe;
    }

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    public function getConsoleCommands()
    {
        return [
            new InstallCommand(
                $this->container->make(Installation::class)
            ),
        ];
    }
}
