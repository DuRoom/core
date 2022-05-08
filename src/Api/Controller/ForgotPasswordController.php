<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use DuRoom\User\Command\RequestPasswordReset;
use DuRoom\User\UserRepository;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ForgotPasswordController implements RequestHandlerInterface
{
    /**
     * @var \DuRoom\User\UserRepository
     */
    protected $users;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param \DuRoom\User\UserRepository $users
     * @param Dispatcher $bus
     */
    public function __construct(UserRepository $users, Dispatcher $bus)
    {
        $this->users = $users;
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $email = Arr::get($request->getParsedBody(), 'email');

        $this->bus->dispatch(
            new RequestPasswordReset($email)
        );

        return new EmptyResponse;
    }
}
