<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Api\Controller;

use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\SerializerInterface;

abstract class AbstractListController extends AbstractSerializeController
{
    /**
     * {@inheritdoc}
     */
    protected function createElement($data, SerializerInterface $serializer)
    {
        return new Collection($data, $serializer);
    }
}
