<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use DuRoom\Api\Serializer\ForumSerializer;
use DuRoom\Group\Group;
use DuRoom\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowForumController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = ForumSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['groups'];

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        return [
            'groups' => Group::whereVisibleTo(RequestUtil::getActor($request))->get()
        ];
    }
}
