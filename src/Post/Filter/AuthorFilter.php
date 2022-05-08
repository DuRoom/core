<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post\Filter;

use DuRoom\Filter\FilterInterface;
use DuRoom\Filter\FilterState;
use DuRoom\User\UserRepository;

class AuthorFilter implements FilterInterface
{
    /**
     * @var \DuRoom\User\UserRepository
     */
    protected $users;

    /**
     * @param \DuRoom\User\UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function getFilterKey(): string
    {
        return 'author';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate)
    {
        $usernames = trim($filterValue, '"');
        $usernames = explode(',', $usernames);

        $ids = $this->users->query()->whereIn('username', $usernames)->pluck('id');

        $filterState->getQuery()->whereIn('posts.user_id', $ids, 'and', $negate);
    }
}
