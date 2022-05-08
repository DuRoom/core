<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Console;

use DuRoom\Foundation\ErrorHandling\Registry;
use DuRoom\Foundation\ErrorHandling\Reporter;
use DuRoom\Foundation\SiteInterface;
use Illuminate\Container\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Server
{
    private $site;

    public function __construct(SiteInterface $site)
    {
        $this->site = $site;
    }

    public function listen()
    {
        $app = $this->site->bootApp();

        $console = new Application('DuRoom', \DuRoom\Foundation\Application::VERSION);

        foreach ($app->getConsoleCommands() as $command) {
            $console->add($command);
        }

        $this->handleErrors($console);

        exit($console->run());
    }

    private function handleErrors(Application $console)
    {
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event) {
            $container = Container::getInstance();

            /** @var Registry $registry */
            $registry = $container->make(Registry::class);
            $error = $registry->handle($event->getError());

            /** @var Reporter[] $reporters */
            $reporters = $container->tagged(Reporter::class);

            if ($error->shouldBeReported()) {
                foreach ($reporters as $reporter) {
                    $reporter->report($error->getException());
                }
            }
        });

        $console->setDispatcher($dispatcher);
    }
}
