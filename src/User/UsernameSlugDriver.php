<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User;

use DuRoom\Database\AbstractModel;
use DuRoom\Http\SlugDriverInterface;

class UsernameSlugDriver implements SlugDriverInterface
{
    /**
     * @var UserRepository
     */
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function toSlug(AbstractModel $instance): string
    {
        return $instance->username;
    }

    public function fromSlug(string $slug, User $actor): AbstractModel
    {
        return $this->users->findOrFailByUsername($slug, $actor);
    }
}
