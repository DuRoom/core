<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Frontend\Compiler;

interface VersionerInterface
{
    public function putRevision(string $file, ?string $revision);

    public function getRevision(string $file): ?string;
}
