<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use DuRoom\Api\Serializer\CurrentUserSerializer;
use DuRoom\Api\Serializer\UserSerializer;
use DuRoom\Http\RequestUtil;
use DuRoom\User\Command\EditUser;
use DuRoom\User\Exception\NotAuthenticatedException;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateUserController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = UserSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['groups'];

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $id = Arr::get($request->getQueryParams(), 'id');
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data', []);

        if ($actor->id == $id) {
            $this->serializer = CurrentUserSerializer::class;
        }

        // Require the user's current password if they are attempting to change
        // their own email address.
        if (isset($data['attributes']['email']) && $actor->id == $id) {
            $password = Arr::get($request->getParsedBody(), 'meta.password');

            if (! $actor->checkPassword($password)) {
                throw new NotAuthenticatedException;
            }
        }

        return $this->bus->dispatch(
            new EditUser($id, $actor, $data)
        );
    }
}
