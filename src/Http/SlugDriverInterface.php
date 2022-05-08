<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Http;

use DuRoom\Database\AbstractModel;
use DuRoom\User\User;

interface SlugDriverInterface
{
    public function toSlug(AbstractModel $instance): string;

    public function fromSlug(string $slug, User $actor): AbstractModel;
}
