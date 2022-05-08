<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Command;

use DuRoom\Foundation\DispatchEventsTrait;
use DuRoom\User\EmailToken;
use DuRoom\User\UserRepository;
use Illuminate\Contracts\Events\Dispatcher;

class ConfirmEmailHandler
{
    use DispatchEventsTrait;

    /**
     * @var \DuRoom\User\UserRepository
     */
    protected $users;

    /**
     * @param \DuRoom\User\UserRepository $users
     */
    public function __construct(Dispatcher $events, UserRepository $users)
    {
        $this->events = $events;
        $this->users = $users;
    }

    /**
     * @param ConfirmEmail $command
     * @return \DuRoom\User\User
     */
    public function handle(ConfirmEmail $command)
    {
        /** @var EmailToken $token */
        $token = EmailToken::validOrFail($command->token);

        $user = $token->user;
        $user->changeEmail($token->email);

        $user->activate();

        $user->save();
        $this->dispatchEventsFor($user);

        // Delete *all* tokens for the user, in case other ones were sent first
        $user->emailTokens()->delete();

        return $user;
    }
}
