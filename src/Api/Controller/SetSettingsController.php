<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use DuRoom\Http\RequestUtil;
use DuRoom\Settings\Event;
use DuRoom\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SetSettingsController implements RequestHandlerInterface
{
    /**
     * @var \DuRoom\Settings\SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param SettingsRepositoryInterface $settings
     */
    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $dispatcher)
    {
        $this->settings = $settings;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        RequestUtil::getActor($request)->assertAdmin();

        $settings = $request->getParsedBody();

        $this->dispatcher->dispatch(new Event\Saving($settings));

        foreach ($settings as $k => $v) {
            $this->dispatcher->dispatch(new Event\Serializing($k, $v));

            $this->settings->set($k, $v);
        }

        $this->dispatcher->dispatch(new Event\Saved($settings));

        return new EmptyResponse(204);
    }
}
