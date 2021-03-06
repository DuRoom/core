<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Forum\Content;

use DuRoom\Frontend\Document;
use DuRoom\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface as Request;

class AssertRegistered
{
    public function __invoke(Document $document, Request $request)
    {
        RequestUtil::getActor($request)->assertRegistered();
    }
}
