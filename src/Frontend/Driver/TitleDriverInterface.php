<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Frontend\Driver;

use DuRoom\Frontend\Document;
use Psr\Http\Message\ServerRequestInterface;

interface TitleDriverInterface
{
    public function makeTitle(Document $document, ServerRequestInterface $request, array $forumApiDocument): string;
}
